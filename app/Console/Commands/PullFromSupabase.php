<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use App\Services\DatabaseQueryService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class PullFromSupabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Usage:
     *   php artisan supabase:pull
     *   php artisan supabase:pull --table=events
     *   php artisan supabase:pull --table=event_registrations
     */
    protected $signature = 'supabase:pull {--table= : Optional: events,event_registrations}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pull events and event registrations from Supabase into SQLite (read-only mirror)';

    public function __construct(private DatabaseQueryService $queryService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Pulling data from Supabase into local SQLite...');

        $tableOption = $this->option('table');
        $tables = $tableOption
            ? array_map('trim', explode(',', $tableOption))
            : ['events', 'event_registrations'];

        foreach ($tables as $table) {
            switch ($table) {
                case 'events':
                    $this->pullEvents();
                    break;
                case 'event_registrations':
                    $this->pullEventRegistrations();
                    break;
                default:
                    $this->warn("Unknown table option: {$table}");
                    break;
            }
        }

        $this->info('Supabase pull completed.');

        return self::SUCCESS;
    }

    /**
     * Pull events from Supabase into local events table.
     *
     * Mapping:
     *  - Supabase events are matched to local events by (title + event_date + location).
     *  - If no local event exists, one is created.
     */
    protected function pullEvents(): void
    {
        $this->info('Pulling events from Supabase...');

        $results = $this->queryService->getAllEventsForSync();

        if (isset($results['error'])) {
            $this->error('Failed to fetch events from Supabase: ' . $results['error']);
            return;
        }

        // Debug: show what we actually got back from Supabase
        if (!is_array($results)) {
            $this->error('Supabase events response is not an array: ' . json_encode($results));
            return;
        }

        $this->line('Supabase returned raw events array of size: ' . count($results));
        if (count($results) > 0) {
            $this->line('First event row from Supabase: ' . json_encode($results[0]));
        }

        if (count($results) === 0) {
            $this->info('No events returned from Supabase.');
            return;
        }

        $count = 0;

        foreach ($results as $row) {
            // Some Supabase responses may wrap rows in a "data" key; normalize.
            if (isset($row['data']) && is_array($row['data'])) {
                $row = $row['data'];
            }

            if (!is_array($row)) {
                continue;
            }

            $title = $row['title'] ?? null;
            $eventDate = $row['event_date'] ?? null;
            $location = $row['location'] ?? '';

            if (!$title) {
                Log::warning('Skipping Supabase event without title', ['row' => $row]);
                continue;
            }

            // Fallbacks so we always have something to store locally
            if (!$eventDate) {
                $eventDate = now()->toDateString();
            }

            // Resolve created_by to a local user ID to satisfy NOT NULL FK constraint
            $createdById = null;
            if (!empty($row['created_by'])) {
                try {
                    $supUser = $this->queryService->getUserById($row['created_by']);
                    if (is_array($supUser) && !isset($supUser['error']) && !empty($supUser['email'])) {
                        $localUser = User::where('email', $supUser['email'])->first();
                        if ($localUser) {
                            $createdById = $localUser->id;
                        }
                    }
                } catch (\Throwable $e) {
                    Log::warning('Failed to resolve created_by from Supabase user', [
                        'error' => $e->getMessage(),
                        'row' => $row,
                    ]);
                }
            }

            // If still null, fall back to the first local user (so inserts don't fail)
            if ($createdById === null) {
                $fallbackUser = User::first();
                $createdById = $fallbackUser?->id;
            }

            try {
                Event::updateOrCreate(
                    [
                        'supabase_event_id' => $row['id'] ?? null,
                    ],
                    [
                        'title' => $title,
                        'date' => $eventDate,
                        'location' => $location,
                        'description' => $row['description'] ?? '',
                        // Local model uses "time" column; Supabase uses "event_time"
                        'time' => $row['event_time'] ?? null,
                        'photo_url' => $row['photo_url'] ?? null,
                        'created_by' => $createdById,
                    ],
                );

                $count++;
            } catch (\Throwable $e) {
                Log::error('Failed to sync single event from Supabase', [
                    'error' => $e->getMessage(),
                    'row' => $row,
                ]);
            }
        }

        $this->info("Pulled {$count} event(s) from Supabase into SQLite.");
    }

    /**
     * Pull event registrations from Supabase into local event_registrations table.
     *
     * Mapping:
     *  - User is resolved by user.email -> local users.email.
     *  - Event is resolved by event.(title + event_date + location) -> local events.(title + date + location).
     *  - If either user or event cannot be resolved, that registration is skipped.
     */
    protected function pullEventRegistrations(): void
    {
        $this->info('Pulling event registrations from Supabase...');

        $results = $this->queryService->getAllEventRegistrationsForSync();

        if (isset($results['error'])) {
            $this->error('Failed to fetch registrations from Supabase: ' . $results['error']);
            return;
        }

        if (!is_array($results)) {
            $this->error('Supabase registrations response is not an array: ' . json_encode($results));
            return;
        }

        // For registrations we expect a flat array of rows
        $rows = $results;

        $this->line('Supabase returned raw registrations array of size: ' . count($rows));

        if (count($rows) === 0) {
            $this->info('No registrations returned from Supabase.');
            return;
        }

        $count = 0;

        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }

            // Resolve/create local user by Supabase user UUID (stable) so we don't collapse rows.
            $supUserId = $row['user_id'] ?? null;
            $user = null;
            if ($supUserId) {
                $user = User::where('supabase_user_id', $supUserId)->first();
            }

            if (!$user) {
                // Try to match an existing local user by email (if we can fetch it), then store supabase_user_id
                $email = null;
                $supUser = null;
                if ($supUserId) {
                    $supUser = $this->queryService->getUserById($supUserId);
                    if (is_array($supUser) && !isset($supUser['error'])) {
                        $email = $supUser['email'] ?? null;
                    }
                }

                if ($email) {
                    $user = User::where('email', $email)->first();
                    if ($user && $supUserId) {
                        $user->supabase_user_id = $supUserId;
                        $user->save();
                    }
                }
            }

            if (!$user) {
                $importEmail = $supUserId
                    ? "imported-{$supUserId}@local.test"
                    : ('imported-' . uniqid() . '@local.test');

                $user = User::firstOrCreate(
                    ['email' => $importEmail],
                    [
                        'name' => 'Imported User',
                        'phone' => null,
                        'password' => Hash::make('password'),
                        'role' => 'volunteer',
                        'notification_pref' => false,
                        'dark_mode' => false,
                        'email_verified_at' => now(),
                        'photo_url' => null,
                        'google_id' => null,
                    ]
                );

                if ($supUserId && empty($user->supabase_user_id)) {
                    $user->supabase_user_id = $supUserId;
                    $user->save();
                }
            }

            // Match by Supabase event UUID stored on the local event row.
            // This avoids any extra Supabase lookups (and avoids RLS/relationship issues).
            $event = null;
            if (!empty($row['event_id'])) {
                $event = Event::where('supabase_event_id', $row['event_id'])->first();
            }

            // Final fallback: attach to the first local event so we at least keep the data
            if (!$event) {
                $event = Event::first();
            }

            if (!$event) {
                Log::warning('Skipping registration; no local events exist to attach', ['row' => $row]);
                continue;
            }

            try {
                EventRegistration::updateOrCreate(
                    [
                        // Use Supabase registration UUID as the unique key for the row.
                        'supabase_registration_id' => $row['id'] ?? null,
                    ],
                    [
                        'user_id' => $user->id,
                        'event_id' => $event->id,
                        'status' => $row['registration_status'] ?? 'pending',
                    ],
                );

                $count++;
            } catch (\Throwable $e) {
                Log::error('Failed to sync single event registration from Supabase', [
                    'error' => $e->getMessage(),
                    'row' => $row,
                ]);
            }
        }

        $this->info("Pulled {$count} registration(s) from Supabase into SQLite.");
    }
}


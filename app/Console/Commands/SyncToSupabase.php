<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\User;
use App\Models\EventRegistration;
use App\Models\Contact;
use App\Models\Member;
use App\Models\Setting;
use App\Services\DatabaseQueryService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncToSupabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'supabase:sync {--table= : Specific table to sync} {--force : Force sync even if data exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Laravel database data to Supabase';

    protected DatabaseQueryService $queryService;

    public function __construct(DatabaseQueryService $queryService)
    {
        parent::__construct();
        $this->queryService = $queryService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Supabase sync...');

        $table = $this->option('table');
        $force = $this->option('force');

        if ($table) {
            $this->syncTable($table, $force);
        } else {
            $this->syncAllTables($force);
        }

        $this->info('Supabase sync completed!');
    }

    protected function syncAllTables(bool $force)
    {
        $tables = ['users', 'events', 'event_registrations', 'contacts', 'members', 'settings'];

        foreach ($tables as $table) {
            $this->syncTable($table, $force);
        }
    }

    protected function syncTable(string $table, bool $force)
    {
        $this->info("Syncing {$table} table...");

        try {
            switch ($table) {
                case 'users':
                    $this->syncUsers($force);
                    break;
                case 'events':
                    $this->syncEvents($force);
                    break;
                case 'event_registrations':
                    $this->syncEventRegistrations($force);
                    break;
                case 'contacts':
                    $this->syncContacts($force);
                    break;
                case 'members':
                    $this->syncMembers($force);
                    break;
                case 'settings':
                    $this->syncSettings($force);
                    break;
                default:
                    $this->error("Unknown table: {$table}");
                    return;
            }

            $this->info("✓ {$table} table synced successfully");
        } catch (\Exception $e) {
            $this->error("✗ Failed to sync {$table} table: " . $e->getMessage());
            Log::error("Supabase sync error for {$table}: " . $e->getMessage());
        }
    }

    protected function syncUsers(bool $force)
    {
        $users = User::all();
        $this->info("Found {$users->count()} users to sync");

        foreach ($users as $user) {
            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role ?? 'volunteer',
                'notification_pref' => $user->notification_pref ?? true,
                'dark_mode' => $user->dark_mode ?? false,
                'email_verified_at' => $user->email_verified_at?->toISOString(),
                'created_at' => $user->created_at->toISOString(),
                'updated_at' => $user->updated_at->toISOString(),
            ];

            try {
                $this->queryService->createEvent($userData);
                $this->line("  - Synced user: {$user->name}");
            } catch (\Exception $e) {
                if ($force) {
                    $this->queryService->updateEvent($user->id, $userData);
                    $this->line("  - Updated user: {$user->name}");
                } else {
                    $this->warn("  - Skipped user: {$user->name} (already exists)");
                }
            }
        }
    }

    protected function syncEvents(bool $force)
    {
        $events = Event::with('creator')->get();
        $this->info("Found {$events->count()} events to sync");

        foreach ($events as $event) {
            $eventData = [
                'id' => $event->id,
                'title' => $event->title,
                'description' => $event->description,
                'event_date' => $event->date->format('Y-m-d'),
                'event_time' => $event->time,
                'location' => $event->location,
                'max_participants' => $event->max_participants,
                'event_status' => $event->status ?? 'active',
                'created_by' => $event->created_by,
                'created_at' => $event->created_at->toISOString(),
                'updated_at' => $event->updated_at->toISOString(),
            ];

            try {
                $this->queryService->createEvent($eventData);
                $this->line("  - Synced event: {$event->title}");
            } catch (\Exception $e) {
                if ($force) {
                    $this->queryService->updateEvent($event->id, $eventData);
                    $this->line("  - Updated event: {$event->title}");
                } else {
                    $this->warn("  - Skipped event: {$event->title} (already exists)");
                }
            }
        }
    }

    protected function syncEventRegistrations(bool $force)
    {
        $registrations = EventRegistration::with(['user', 'event'])->get();
        $this->info("Found {$registrations->count()} event registrations to sync");

        foreach ($registrations as $registration) {
            $registrationData = [
                'id' => $registration->id,
                'user_id' => $registration->user_id,
                'event_id' => $registration->event_id,
                'registration_status' => $registration->status ?? 'pending',
                'notes' => $registration->notes,
                'created_at' => $registration->created_at->toISOString(),
                'updated_at' => $registration->updated_at->toISOString(),
            ];

            try {
                $this->queryService->registerForEvent(
                    $registration->user_id,
                    $registration->event_id,
                    $registrationData
                );
                $this->line("  - Synced registration: {$registration->user->name} -> {$registration->event->title}");
            } catch (\Exception $e) {
                if ($force) {
                    $this->queryService->updateRegistrationStatus($registration->id, $registration->status);
                    $this->line("  - Updated registration: {$registration->user->name} -> {$registration->event->title}");
                } else {
                    $this->warn("  - Skipped registration: {$registration->user->name} -> {$registration->event->title} (already exists)");
                }
            }
        }
    }

    protected function syncContacts(bool $force)
    {
        $contacts = Contact::all();
        $this->info("Found {$contacts->count()} contacts to sync");

        foreach ($contacts as $contact) {
            $contactData = [
                'id' => $contact->id,
                'name' => $contact->name,
                'email' => $contact->email,
                'phone' => $contact->phone,
                'subject' => $contact->subject,
                'message' => $contact->message,
                'contact_status' => $contact->status ?? 'new',
                'created_at' => $contact->created_at->toISOString(),
                'updated_at' => $contact->updated_at->toISOString(),
            ];

            try {
                $this->queryService->createEvent($contactData);
                $this->line("  - Synced contact: {$contact->name}");
            } catch (\Exception $e) {
                if ($force) {
                    $this->queryService->updateEvent($contact->id, $contactData);
                    $this->line("  - Updated contact: {$contact->name}");
                } else {
                    $this->warn("  - Skipped contact: {$contact->name} (already exists)");
                }
            }
        }
    }

    protected function syncMembers(bool $force)
    {
        $members = Member::all();
        $this->info("Found {$members->count()} members to sync");

        foreach ($members as $member) {
            $memberData = [
                'id' => $member->id,
                'name' => $member->name,
                'email' => $member->email,
                'phone' => $member->phone,
                'address' => $member->address,
                'skills' => $member->skills,
                'availability' => $member->availability,
                'emergency_contact_name' => $member->emergency_contact_name,
                'emergency_contact_phone' => $member->emergency_contact_phone,
                'member_status' => $member->status ?? 'active',
                'created_at' => $member->created_at->toISOString(),
                'updated_at' => $member->updated_at->toISOString(),
            ];

            try {
                $this->queryService->createEvent($memberData);
                $this->line("  - Synced member: {$member->name}");
            } catch (\Exception $e) {
                if ($force) {
                    $this->queryService->updateEvent($member->id, $memberData);
                    $this->line("  - Updated member: {$member->name}");
                } else {
                    $this->warn("  - Skipped member: {$member->name} (already exists)");
                }
            }
        }
    }

    protected function syncSettings(bool $force)
    {
        $settings = Setting::all();
        $this->info("Found {$settings->count()} settings to sync");

        foreach ($settings as $setting) {
            $settingData = [
                'id' => $setting->id,
                'setting_key' => $setting->key,
                'setting_value' => $setting->value,
                'setting_type' => $setting->type ?? 'string',
                'description' => $setting->description,
                'created_at' => $setting->created_at->toISOString(),
                'updated_at' => $setting->updated_at->toISOString(),
            ];

            try {
                $this->queryService->createEvent($settingData);
                $this->line("  - Synced setting: {$setting->key}");
            } catch (\Exception $e) {
                if ($force) {
                    $this->queryService->updateEvent($setting->id, $settingData);
                    $this->line("  - Updated setting: {$setting->key}");
                } else {
                    $this->warn("  - Skipped setting: {$setting->key} (already exists)");
                }
            }
        }
    }
}

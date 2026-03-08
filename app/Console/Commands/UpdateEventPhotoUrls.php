<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Services\DatabaseQueryService;
use Illuminate\Support\Facades\Log;

class UpdateEventPhotoUrls extends Command
{
    protected $signature = 'events:update-photo-urls';
    protected $description = 'Update existing events with photo_url based on files in storage';

    protected $queryService;

    public function __construct(DatabaseQueryService $queryService)
    {
        parent::__construct();
        $this->queryService = $queryService;
    }

    public function handle()
    {
        $this->info('Updating event photo URLs...');

        // Get all events from Supabase
        $events = $this->queryService->getEvents(1, 1000);

        if (isset($events['error'])) {
            $this->error('Failed to fetch events: ' . $events['error']);
            return 1;
        }

        if (!is_array($events) || count($events) === 0) {
            $this->info('No events found.');
            return 0;
        }

        // Get all files in storage/app/public/events
        $files = Storage::disk('public')->files('events');
        
        $this->info('Found ' . count($events) . ' events and ' . count($files) . ' image files.');
        
        // Debug: Show first event structure
        $firstEvent = reset($events);
        if ($firstEvent) {
            $this->line('First event structure: ' . json_encode($firstEvent, JSON_PRETTY_PRINT));
        }

        $updated = 0;
        $skipped = 0;

        foreach ($events as $event) {
            $eventId = $event['id'] ?? null;
            $eventTitle = $event['title'] ?? 'Unknown';
            $currentPhotoUrl = $event['photo_url'] ?? null;

            if (!$eventId) {
                $this->warn("Skipping event without ID: {$eventTitle}");
                continue;
            }

            // Skip if already has photo_url
            if (!empty($currentPhotoUrl) && $currentPhotoUrl !== 'null') {
                $this->line("Skipping '{$eventTitle}' - already has photo_url: {$currentPhotoUrl}");
                $skipped++;
                continue;
            }

            // Try to find matching file based on event title
            $slug = \Illuminate\Support\Str::slug($eventTitle);
            $matchingFile = null;

            // First try exact match with slug
            foreach ($files as $file) {
                $filename = basename($file);
                $filenameWithoutExt = pathinfo($filename, PATHINFO_FILENAME);
                
                // Check if filename starts with the slug
                if (str_starts_with($filenameWithoutExt, $slug)) {
                    $matchingFile = $file;
                    break;
                }
            }

            // If no match, try partial match (for cases like "trying" matching "try-")
            if (!$matchingFile && $slug) {
                foreach ($files as $file) {
                    $filename = basename($file);
                    $filenameWithoutExt = pathinfo($filename, PATHINFO_FILENAME);
                    
                    // Check if slug is contained in filename or vice versa
                    if (str_contains($filenameWithoutExt, $slug) || str_contains($slug, $filenameWithoutExt)) {
                        $matchingFile = $file;
                        break;
                    }
                }
            }

            if ($matchingFile) {
                $photoUrl = Storage::url($matchingFile);
                
                $this->info("Updating '{$eventTitle}' (ID: {$eventId}) with photo: {$photoUrl}");
                
                // Update event in Supabase
                $result = $this->queryService->updateEvent($eventId, [
                    'photo_url' => $photoUrl,
                ]);

                if (isset($result['error'])) {
                    $this->error("Failed to update '{$eventTitle}': " . $result['error']);
                } else {
                    $this->info("✓ Successfully updated '{$eventTitle}'");
                    $updated++;
                }
            } else {
                $this->line("No matching file found for '{$eventTitle}' (slug: {$slug})");
            }
        }

        $this->info("\nUpdate complete!");
        $this->info("Updated: {$updated}");
        $this->info("Skipped: {$skipped}");

        return 0;
    }
}

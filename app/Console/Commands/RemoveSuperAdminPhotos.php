<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Services\DatabaseQueryService;

class RemoveSuperAdminPhotos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:remove-super-admin-photos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove profile photos from all super admin users';

    /**
     * Execute the console command.
     */
    public function handle(DatabaseQueryService $queryService)
    {
        $this->info('Removing profile photos from super admin users...');

        $superAdmins = User::where('role', 'superadmin')
            ->whereNotNull('photo_url')
            ->get();

        if ($superAdmins->isEmpty()) {
            $this->info('No super admin users with photos found.');
            return 0;
        }

        $count = 0;
        foreach ($superAdmins as $user) {
            // Delete photo file from storage
            if ($user->photo_url) {
                $oldPath = str_replace('/storage/', '', $user->photo_url);
                Storage::disk('public')->delete($oldPath);
            }

            // Remove photo_url from database
            $user->photo_url = null;
            $user->save();

            // Update Supabase
            $updateData = [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? null,
                'google_id' => $user->google_id ?? null,
                'role' => $user->role,
                'notification_pref' => $user->notification_pref ?? true,
                'dark_mode' => $user->dark_mode ?? false,
                'email_verified_at' => $user->email_verified_at?->toISOString(),
                'photo_url' => null,
            ];

            $result = $queryService->upsertUser($updateData);
            
            if (isset($result['error'])) {
                $this->warn("Failed to sync user {$user->email} to Supabase: " . ($result['error'] ?? 'Unknown error'));
            } else {
                $this->info("Removed photo from: {$user->name} ({$user->email})");
                $count++;
            }
        }

        $this->info("Successfully removed photos from {$count} super admin user(s).");
        return 0;
    }
}

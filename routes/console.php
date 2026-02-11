<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schedule;
use App\Models\User;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('admin:create {email} {--name=Admin} {--password=} {--role=superadmin}', function (string $email) {
    $name = (string) $this->option('name');
    $password = (string) $this->option('password');
    $role = (string) $this->option('role');

    // Validate role - allow both admin and superadmin (they are equivalent)
    if (!in_array($role, ['admin', 'superadmin'])) {
        $this->error('Role must be either "admin" or "superadmin"');
        return;
    }

    if ($password === '') {
        $password = bin2hex(random_bytes(8));
        $this->warn('No password provided. Generated temporary password: ' . $password);
    }

    $user = User::updateOrCreate(
        ['email' => $email],
        [
            'name' => $name,
            'password' => Hash::make($password),
            'role' => $role,
        ]
    );

    $this->info('Admin user ready.');
    $this->line('Email: ' . $user->email);
    $this->line('Name: ' . $user->name);
    $this->line('Role: ' . $user->role);
    $this->comment('Note: "admin" and "superadmin" roles have the same permissions.');
})->purpose('Create or update an admin/superadmin user');

// Schedule: Send event reminders daily at 9:00 AM
Schedule::command('events:send-reminders')
    ->dailyAt('09:00')
    ->description('Send email reminders to volunteers for events happening tomorrow');

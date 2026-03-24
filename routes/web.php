<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\EventRegistrationController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\MessagingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\MessagingController as SharedMessagingController;
use App\Http\Controllers\VolunteerDashboardController;
use App\Http\Controllers\SupabaseController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/calendar', [EventController::class, 'calendar'])->name('events.calendar');
// Accept UUIDs for Supabase events (must come before /events/create to avoid conflicts)
Route::get('/events/{eventId}', [EventController::class, 'show'])->where('eventId', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}')->name('events.show');
Route::get('/members', [MemberController::class, 'index'])->name('members.index');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
Route::view('/terms', 'legal.terms')->name('terms.show');
Route::view('/privacy', 'legal.privacy')->name('privacy.show');

// Supabase API routes (for testing)
Route::get('/supabase/events', [SupabaseController::class, 'getEvents'])->name('supabase.events');
Route::post('/supabase/events', [SupabaseController::class, 'createEvent'])->name('supabase.create-event');
Route::post('/supabase/upload', [SupabaseController::class, 'uploadFile'])->name('supabase.upload');

// Dashboard route (redirects based on user role)
Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->isAdmin() || $user->isPresident()) {
        // Admins/President → admin dashboard
        return redirect()->route('admin.dashboard');
    } elseif ($user->isVolunteer()) {
        // Volunteers → events list
        return redirect()->route('events.index');
    }

    // Fallback for other roles: send to home
    return redirect()->route('home');
})->middleware(['auth'])->name('dashboard');

// Authentication routes
require __DIR__.'/auth.php';

// Protected routes
Route::middleware('auth')->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Members routes (authenticated users only)
    Route::get('/members', [MemberController::class, 'index'])->name('members.index');
    
    // Member management routes (admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/members/create', [MemberController::class, 'create'])->name('members.create');
        Route::post('/members', [MemberController::class, 'store'])->name('members.store');
        Route::get('/members/{member}/edit', [MemberController::class, 'edit'])->name('members.edit');
        Route::put('/members/{member}', [MemberController::class, 'update'])->name('members.update');
        Route::delete('/members/{member}', [MemberController::class, 'destroy'])->name('members.destroy');
    });
    
    // Settings route
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
    
    // Event registration routes (Supabase UUID-based)
    Route::post('/events/{eventId}/join', [EventRegistrationController::class, 'join'])
        ->name('events.join')
        ->where('eventId', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');
    Route::delete('/events/{eventId}/leave', [EventRegistrationController::class, 'leave'])
        ->name('events.leave')
        ->where('eventId', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');

    // Notifications (for all authenticated users)
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notificationId}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    
    // Volunteer Dashboard
    Route::get('/volunteer/dashboard', [VolunteerDashboardController::class, 'index'])->name('volunteer.dashboard');
    
    // Messaging routes (for all authenticated users)
    Route::get('/messaging', [SharedMessagingController::class, 'index'])->name('messaging');
    Route::get('/messaging/chat/{user}', [SharedMessagingController::class, 'chat'])->name('messaging.chat');
    Route::post('/messaging/send', [SharedMessagingController::class, 'send'])->name('messaging.send');
    Route::put('/messaging/{message}', [SharedMessagingController::class, 'update'])->name('messaging.update');
    Route::delete('/messaging/{message}', [SharedMessagingController::class, 'destroy'])->name('messaging.destroy');
    
    // Admin routes (Events, Users, Attendance, Approvals)
    Route::middleware(['role:admin', 'auth'])->group(function () {
        Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        
        // User Management (Admin)
        Route::resource('admin/users', UserController::class)->names([
            'index' => 'admin.users.index',
            'create' => 'admin.users.create',
            'store' => 'admin.users.store',
            'show' => 'admin.users.show',
            'edit' => 'admin.users.edit',
            'update' => 'admin.users.update',
            'destroy' => 'admin.users.destroy',
        ]);
        
        // Event management (Admin)
        Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
        Route::post('/events', [EventController::class, 'store'])->name('events.store');
        // Use UUID string instead of route model binding for Supabase events
        // UUID pattern: 8-4-4-4-12 hex characters with hyphens
        Route::get('/events/{eventId}/edit', [EventController::class, 'edit'])->name('events.edit')->where('eventId', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');
        Route::put('/events/{eventId}', [EventController::class, 'update'])->name('events.update')->where('eventId', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');
        Route::delete('/events/{eventId}', [EventController::class, 'destroy'])->name('events.destroy')->where('eventId', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');
        
        // Attendance (Admin)
        Route::get('/admin/attendance', [AttendanceController::class, 'index'])->name('admin.attendance');
        
        // Event registration management (Admin)
        // Legacy MySQL-backed approvals
        Route::patch('/registrations/{registration}/approve', [EventRegistrationController::class, 'approve'])->name('registrations.approve');
        Route::patch('/registrations/{registration}/reject', [EventRegistrationController::class, 'reject'])->name('registrations.reject');

        // Supabase-backed approvals for registrations loaded from Supabase (per-event admin view)
        Route::patch('/supabase/registrations/{registrationId}/approve', [EventRegistrationController::class, 'approveSupabase'])
            ->name('supabase.registrations.approve');
        Route::patch('/supabase/registrations/{registrationId}/reject', [EventRegistrationController::class, 'rejectSupabase'])
            ->name('supabase.registrations.reject');

        // Bulk registration actions (Admin)
        Route::post('/registrations/bulk-approve', [EventRegistrationController::class, 'bulkApprove'])->name('registrations.bulk-approve');
        Route::post('/registrations/bulk-reject', [EventRegistrationController::class, 'bulkReject'])->name('registrations.bulk-reject');

        // Supabase bulk registration actions (UUIDs)
        Route::post('/supabase/registrations/bulk-approve', [EventRegistrationController::class, 'bulkApproveSupabase'])
            ->name('supabase.registrations.bulk-approve');
        Route::post('/supabase/registrations/bulk-reject', [EventRegistrationController::class, 'bulkRejectSupabase'])
            ->name('supabase.registrations.bulk-reject');
    });

    // Admin-only routes
    Route::middleware(['role:admin', 'auth'])->group(function () {
        // Admin Messaging
        Route::get('/admin/messaging', [MessagingController::class, 'index'])->name('admin.messaging');
        Route::get('/admin/messaging/chat/{user}', [MessagingController::class, 'chat'])->name('admin.messaging.chat');
        Route::post('/admin/messaging/send', [MessagingController::class, 'send'])->name('admin.messaging.send');
        Route::put('/admin/messaging/{message}', [MessagingController::class, 'update'])->name('admin.messaging.update');
        Route::delete('/admin/messaging/{message}', [MessagingController::class, 'destroy'])->name('admin.messaging.destroy');
        
        // Reports
        Route::get('/admin/reports', [ReportController::class, 'index'])->name('admin.reports');
        Route::get('/admin/reports/export', [ReportController::class, 'export'])->name('admin.reports.export');

        // Audit logs
        Route::get('/admin/audit-logs', [AuditLogController::class, 'index'])->name('admin.audit-logs.index');
    });
});

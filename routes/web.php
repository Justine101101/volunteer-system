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
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/calendar', [EventController::class, 'calendar'])->name('events.calendar');
// Constrain to numeric to avoid clashing with '/events/create'
Route::get('/events/{event}', [EventController::class, 'show'])->whereNumber('event')->name('events.show');
Route::get('/members', [MemberController::class, 'index'])->name('members.index');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// Supabase API routes (for testing)
Route::get('/supabase/events', [SupabaseController::class, 'getEvents'])->name('supabase.events');
Route::post('/supabase/events', [SupabaseController::class, 'createEvent'])->name('supabase.create-event');
Route::post('/supabase/upload', [SupabaseController::class, 'uploadFile'])->name('supabase.upload');

// Dashboard route (redirects based on user role)
Route::get('/dashboard', function () {
    $user = auth()->user();
    
    
    if ($user->isSuperAdmin() || $user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    } elseif ($user->isVolunteer()) {
        return redirect()->route('volunteer.dashboard');
    }
    
    // Fallback for other roles or default dashboard
    return view('dashboard');
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
    
    // Member management routes (superadmin only)
    Route::middleware('role:superadmin')->group(function () {
        Route::get('/members/create', [MemberController::class, 'create'])->name('members.create');
        Route::post('/members', [MemberController::class, 'store'])->name('members.store');
        Route::get('/members/{member}/edit', [MemberController::class, 'edit'])->name('members.edit');
        Route::put('/members/{member}', [MemberController::class, 'update'])->name('members.update');
        Route::delete('/members/{member}', [MemberController::class, 'destroy'])->name('members.destroy');
    });
    
    // Settings route
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
    
    // Event registration routes
    Route::post('/events/{event}/join', [EventRegistrationController::class, 'join'])->name('events.join');
    Route::delete('/events/{event}/leave', [EventRegistrationController::class, 'leave'])->name('events.leave');
    
    // Volunteer Dashboard
    Route::get('/volunteer/dashboard', [VolunteerDashboardController::class, 'index'])->name('volunteer.dashboard');
    
    // Messaging routes (for all authenticated users)
    Route::get('/messaging', [SharedMessagingController::class, 'index'])->name('messaging');
    Route::get('/messaging/chat/{user}', [SharedMessagingController::class, 'chat'])->name('messaging.chat');
    Route::post('/messaging/send', [SharedMessagingController::class, 'send'])->name('messaging.send');
    Route::put('/messaging/{message}', [SharedMessagingController::class, 'update'])->name('messaging.update');
    Route::delete('/messaging/{message}', [SharedMessagingController::class, 'destroy'])->name('messaging.destroy');
    
    // Admin and Superadmin shared routes (Events, Users, Attendance, Approvals)
    Route::middleware(['role:admin', 'auth'])->group(function () {
        Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        
        // User Management (Admin and Superadmin)
        Route::resource('admin/users', UserController::class)->names([
            'index' => 'admin.users.index',
            'create' => 'admin.users.create',
            'store' => 'admin.users.store',
            'edit' => 'admin.users.edit',
            'update' => 'admin.users.update',
            'destroy' => 'admin.users.destroy',
        ]);
        
        // Event management (Admin and Superadmin)
        Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
        Route::post('/events', [EventController::class, 'store'])->name('events.store');
        Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
        Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
        Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
        
        // Attendance (Admin and Superadmin)
        Route::get('/admin/attendance', [AttendanceController::class, 'index'])->name('admin.attendance');
        
        // Event registration management (Admin and Superadmin)
        Route::patch('/registrations/{registration}/approve', [EventRegistrationController::class, 'approve'])->name('registrations.approve');
        Route::patch('/registrations/{registration}/reject', [EventRegistrationController::class, 'reject'])->name('registrations.reject');

        // Bulk registration actions (Admin and Superadmin)
        Route::post('/registrations/bulk-approve', [EventRegistrationController::class, 'bulkApprove'])->name('registrations.bulk-approve');
        Route::post('/registrations/bulk-reject', [EventRegistrationController::class, 'bulkReject'])->name('registrations.bulk-reject');
    });

    // Superadmin only routes
    Route::middleware(['role:superadmin', 'auth'])->group(function () {
        // Admin Messaging (Superadmin only)
        Route::get('/admin/messaging', [MessagingController::class, 'index'])->name('admin.messaging');
        Route::get('/admin/messaging/chat/{user}', [MessagingController::class, 'chat'])->name('admin.messaging.chat');
        Route::post('/admin/messaging/send', [MessagingController::class, 'send'])->name('admin.messaging.send');
        Route::put('/admin/messaging/{message}', [MessagingController::class, 'update'])->name('admin.messaging.update');
        Route::delete('/admin/messaging/{message}', [MessagingController::class, 'destroy'])->name('admin.messaging.destroy');
        
        // Reports (Superadmin only)
        Route::get('/admin/reports', [ReportController::class, 'index'])->name('admin.reports');

        // Audit logs (Superadmin only)
        Route::get('/admin/audit-logs', [AuditLogController::class, 'index'])->name('admin.audit-logs.index');
    });
});

<?php

use App\Http\Controllers\SupabaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public API routes
Route::get('/test-connection', [SupabaseController::class, 'testConnection']);

// Protected API routes
Route::middleware('auth:sanctum')->group(function () {
    
    // Events API
    Route::prefix('events')->group(function () {
        Route::get('/', [SupabaseController::class, 'getEvents']);
        Route::post('/', [SupabaseController::class, 'createEvent']);
        Route::get('/{id}', [SupabaseController::class, 'getEvent']);
        Route::put('/{id}', [SupabaseController::class, 'updateEvent']);
        Route::delete('/{id}', [SupabaseController::class, 'deleteEvent']);
    });

    // Users API
    Route::prefix('users')->group(function () {
        Route::get('/', [SupabaseController::class, 'getUsers']);
        Route::get('/{id}', [SupabaseController::class, 'getUser']);
    });

    // Event Registrations API
    Route::prefix('registrations')->group(function () {
        Route::get('/', [SupabaseController::class, 'getEventRegistrations']);
        Route::post('/register', [SupabaseController::class, 'registerForEvent']);
        Route::put('/{id}/status', [SupabaseController::class, 'updateRegistrationStatus']);
    });

    // Dashboard & Analytics API
    Route::get('/dashboard/stats', [SupabaseController::class, 'getDashboardStats']);
    Route::get('/analytics', [SupabaseController::class, 'getAnalytics']);
    Route::get('/search', [SupabaseController::class, 'search']);

    // File Upload API
    Route::post('/upload', [SupabaseController::class, 'uploadFile']);
});

// Admin only routes
Route::middleware(['auth:sanctum', 'role:superadmin'])->group(function () {
    // Additional admin-specific API routes can be added here
    Route::get('/admin/analytics', [SupabaseController::class, 'getAnalytics']);
});

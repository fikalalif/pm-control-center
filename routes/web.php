<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\ChangeRequestController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\MilestoneController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectExportController;
use App\Http\Controllers\RiskController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// ==========================================
// PUBLIC ROUTES[cite: 5]
// ==========================================
Route::get('/', function () {
    return redirect()->route('login');
});

// ==========================================
// AUTHENTICATED ROUTES[cite: 5]
// ==========================================
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard[cite: 5]
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Management[cite: 5]
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    // Resource Management dengan proteksi Middleware Permission Spatie
    Route::resource('clients', ClientController::class);
    Route::resource('projects', ProjectController::class);
    Route::resource('tasks', TaskController::class);
    Route::resource('milestones', MilestoneController::class);
    Route::resource('risks', RiskController::class);
    Route::resource('issues', IssueController::class);
    Route::resource('change-requests', ChangeRequestController::class)
        ->parameters(['change-requests' => 'changeRequest']);
    Route::resource('vendors', VendorController::class);
    Route::resource('meetings', MeetingController::class);

    // Proteksi khusus modul Team dan Roles
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class)->except(['create', 'show', 'edit']);

    // Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings/global', [SettingController::class, 'updateGlobal'])->name('settings.update.global');
    Route::post('/settings/personal', [SettingController::class, 'updatePersonal'])->name('settings.update.personal');
    Route::post('/settings', [SettingController::class, 'updateGlobal'])->name('settings.update');

    // Activity Log[cite: 5]
    Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');

    // Exports[cite: 5]
    Route::get('/projects/{project}/export/pdf', [ProjectExportController::class, 'exportPdf'])->name('projects.export.pdf');

    // Notifications[cite: 5]
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('read-all');
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
    });
});

// ==========================================
// AUTHENTICATION ROUTES (Breeze / Jetstream)[cite: 5]
// ==========================================
require __DIR__ . '/auth.php';

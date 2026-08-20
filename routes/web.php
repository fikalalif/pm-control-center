<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\ChangeRequestController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\MilestoneController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClientController; // Tambahkan import ini
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RiskController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Tambahkan resource route untuk clients di sini
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
    Route::resource('users', UserController::class);
    Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    
    // Exports
    Route::get('/projects/{project}/export/pdf', [\App\Http\Controllers\ProjectExportController::class, 'exportPdf'])->name('projects.export.pdf');

    // Notifications
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
});

require __DIR__ . '/auth.php';

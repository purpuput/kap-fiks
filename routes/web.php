<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('auth.login');
});

Route::middleware('auth')->get('/dashboard', function () {
    session()->reflash();
    return match (Auth::user()->role) {
        'admin'    => redirect()->route('admin.dashboard'),
        'auditor'  => redirect()->route('auditor.dashboard'),
        'pimpinan' => redirect()->route('pimpinan.dashboard'),
        default    => abort(403, 'Unauthorized action.'),
    };
})->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.read_all');
    Route::get('/notifications/unread-data', [\App\Http\Controllers\NotificationController::class, 'unreadData'])->name('notifications.unread_data');
});

require __DIR__ . '/auth.php';

// ADMIN
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])
        ->name('admin.dashboard');
});

// AUDITOR
Route::middleware(['auth', 'role:auditor'])->group(function () {
    Route::get('/auditor/dashboard', [DashboardController::class, 'auditor'])
        ->name('auditor.dashboard');
});

// PIMPINAN KANTOR
Route::middleware(['auth', 'role:pimpinan'])->group(function () {
    Route::get('/pimpinan/dashboard', [DashboardController::class, 'pimpinan'])
        ->name('pimpinan.dashboard');
});

require __DIR__ . '/admin.php';
require __DIR__ . '/auditor.php';
require __DIR__ . '/pimpinan.php';
require __DIR__ . '/features.php';

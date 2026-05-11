<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');

    Route::get('/users', function () {
        return view('admin.users.index');
    })->name('users.index');

    Route::get('/users/create', function () {
        return view('admin.users.create');
    })->name('users.create');

    Route::get('/users/{user}/edit', function ($user) {
        return view('admin.users.edit', compact('user'));
    })->name('users.edit');

    // User Management Routes
    Route::resource('users', UserController::class);
});

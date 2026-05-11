<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:auditor'])->group(function () {
    Route::get('/auditor/dashboard', [DashboardController::class, 'auditor'])
        ->name('auditor.dashboard');
});

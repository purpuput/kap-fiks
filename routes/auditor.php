<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:auditor'])->group(function () {
    Route::get('/auditor/dashboard', function () {
        return view('auditor.dashboard');
    })->name('auditor.dashboard');
    
    // Tambahkan routes auditor lainnya di bawah ini
});

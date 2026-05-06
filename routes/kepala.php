<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:kepala_kantor'])->group(function () {
    Route::get('/kepala/dashboard', function () {
        return view('kepala.dashboard');
    })->name('kepala.dashboard');
    
    // Tambahkan routes kepala kantor lainnya di bawah ini
});

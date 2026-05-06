<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:karyawan'])->group(function () {
    Route::get('/karyawan/dashboard', function () {
        return view('karyawan.dashboard');
    })->name('karyawan.dashboard');
    
    // Tambahkan routes karyawan lainnya di bawah ini
});

<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:pimpinan'])->group(function () {
    Route::get('/pimpinan/dashboard', function () {
        return view('pimpinan.dashboard');
    })->name('pimpinan.dashboard');
    
    // Tambahkan routes pimpinan lainnya di bawah ini
});

<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('auth.login');
});

Route::middleware('auth')->get('/dashboard', function () {

    return match (Auth::user()->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'auditor' => redirect()->route('auditor.dashboard'),
        'pimpinan' => redirect()->route('pimpinan.dashboard'),
        default => abort(403, 'Unauthorized action.'),
    };
})->name('dashboard');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

//ADMIN
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    });
});

//AUDITOR
Route::middleware(['auth', 'role:auditor'])->group(function () {
    Route::get('/auditor/dashboard', function () {
        return view('auditor.dashboard');
    });
});

//PIMPINAN KANTOR
Route::middleware(['auth', 'role:pimpinan'])->group(function () {
    Route::get('/pimpinan/dashboard', function () {
        return view('pimpinan.dashboard');
    });
});


require __DIR__ . '/admin.php';
require __DIR__ . '/auditor.php';
require __DIR__ . '/pimpinan.php';
require __DIR__ . '/features.php';

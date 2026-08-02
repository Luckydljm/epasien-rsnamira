<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\RajalController;
use App\Http\Controllers\RanapController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ePasien RS Namira — Routes
|--------------------------------------------------------------------------
*/

// === AUTH ROUTES ===
Route::middleware('guest.check')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout')
    ->middleware('auth.check');

// === PROTECTED ROUTES (butuh login) ===
Route::middleware('auth.check')->group(function () {
    Route::get('/', fn () => redirect()->route('portal'));
    Route::get('/portal', [PortalController::class, 'index'])->name('portal');
    Route::get('/portal/select/{mode}', [PortalController::class, 'selectMode'])->name('portal.select');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Layanan
    Route::get('/rawat-jalan', [RajalController::class, 'index'])->name('rawat-jalan');
    Route::get('/rawat-inap',  [RanapController::class,  'index'])->name('rawat-inap');
});


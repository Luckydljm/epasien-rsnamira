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

    // Rawat Jalan & Pelayanan Dokter (Offcanvas)
    Route::get('/rawat-jalan', [RajalController::class, 'index'])->name('rawat-jalan');
    Route::get('/rawat-jalan/pasien-detail/{no_rawat}', [RajalController::class, 'getPasienDetail'])->name('rawat-jalan.pasien-detail');

    // 1. SOAP
    Route::post('/rawat-jalan/simpan-soap', [RajalController::class, 'simpanSoap'])->name('rawat-jalan.simpan-soap');

    // 2. Awal Medis
    Route::post('/rawat-jalan/simpan-awal-medis', [RajalController::class, 'simpanAwalMedis'])->name('rawat-jalan.simpan-awal-medis');

    // 3. Laboratorium
    Route::get('/rawat-jalan/master-lab', [RajalController::class, 'masterLab'])->name('rawat-jalan.master-lab');
    Route::post('/rawat-jalan/simpan-lab', [RajalController::class, 'simpanLab'])->name('rawat-jalan.simpan-lab');

    // 4. Radiologi
    Route::get('/rawat-jalan/master-radiologi', [RajalController::class, 'masterRadiologi'])->name('rawat-jalan.master-radiologi');
    Route::post('/rawat-jalan/simpan-radiologi', [RajalController::class, 'simpanRadiologi'])->name('rawat-jalan.simpan-radiologi');

    // 5. Resep Dokter
    Route::get('/rawat-jalan/master-obat', [RajalController::class, 'masterObat'])->name('rawat-jalan.master-obat');
    Route::post('/rawat-jalan/simpan-resep', [RajalController::class, 'simpanResep'])->name('rawat-jalan.simpan-resep');

    // 6. Jadwal Operasi & Master Dokter
    Route::get('/rawat-jalan/master-operasi', [RajalController::class, 'masterOperasi'])->name('rawat-jalan.master-operasi');
    Route::get('/rawat-jalan/master-dokter', [RajalController::class, 'masterDokter'])->name('rawat-jalan.master-dokter');
    Route::post('/rawat-jalan/simpan-booking-operasi', [RajalController::class, 'simpanBookingOperasi'])->name('rawat-jalan.simpan-booking-operasi');

    // Rawat Inap
    Route::get('/rawat-inap',  [RanapController::class,  'index'])->name('rawat-inap');
});

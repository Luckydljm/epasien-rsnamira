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

    // ===================================================================
    // RAWAT JALAN
    // ===================================================================
    Route::get('/rawat-jalan', [RajalController::class, 'index'])->name('rawat-jalan');
    Route::get('/rawat-jalan/pasien-detail/{no_rawat?}', [RajalController::class, 'getPasienDetail'])
        ->where('no_rawat', '.*')
        ->name('rawat-jalan.pasien-detail');

    // SOAP Rajal
    Route::post('/rawat-jalan/simpan-soap', [RajalController::class, 'simpanSoap'])->name('rawat-jalan.simpan-soap');
    Route::delete('/rawat-jalan/hapus-soap', [RajalController::class, 'hapusSoap'])->name('rawat-jalan.hapus-soap');

    // Awal Medis Rajal
    Route::post('/rawat-jalan/simpan-awal-medis', [RajalController::class, 'simpanAwalMedis'])->name('rawat-jalan.simpan-awal-medis');

    // Laboratorium Rajal
    Route::get('/rawat-jalan/master-lab', [RajalController::class, 'masterLab'])->name('rawat-jalan.master-lab');
    Route::post('/rawat-jalan/simpan-lab', [RajalController::class, 'simpanLab'])->name('rawat-jalan.simpan-lab');

    // Radiologi Rajal
    Route::get('/rawat-jalan/master-radiologi', [RajalController::class, 'masterRadiologi'])->name('rawat-jalan.master-radiologi');
    Route::post('/rawat-jalan/simpan-radiologi', [RajalController::class, 'simpanRadiologi'])->name('rawat-jalan.simpan-radiologi');

    // Resep Dokter Rajal
    Route::get('/rawat-jalan/master-obat', [RajalController::class, 'masterObat'])->name('rawat-jalan.master-obat');
    Route::get('/rawat-jalan/master-aturan-pakai', [RajalController::class, 'masterAturanPakai'])->name('rawat-jalan.master-aturan-pakai');
    Route::post('/rawat-jalan/simpan-resep', [RajalController::class, 'simpanResep'])->name('rawat-jalan.simpan-resep');
    Route::delete('/rawat-jalan/hapus-resep', [RajalController::class, 'hapusResep'])->name('rawat-jalan.hapus-resep');

    // Jadwal Operasi, Laporan Operasi & Master OK Rajal
    Route::get('/rawat-jalan/master-operasi', [RajalController::class, 'masterOperasi'])->name('rawat-jalan.master-operasi');
    Route::get('/rawat-jalan/master-dokter', [RajalController::class, 'masterDokter'])->name('rawat-jalan.master-dokter');
    Route::get('/rawat-jalan/master-ruang-ok', [RajalController::class, 'masterRuangOk'])->name('rawat-jalan.master-ruang-ok');
    Route::get('/rawat-jalan/master-template-laporan-operasi', [RajalController::class, 'masterTemplateLaporanOperasi'])->name('rawat-jalan.master-template-laporan-operasi');
    Route::post('/rawat-jalan/simpan-booking-operasi', [RajalController::class, 'simpanBookingOperasi'])->name('rawat-jalan.simpan-booking-operasi');
    Route::delete('/rawat-jalan/hapus-booking-operasi', [RajalController::class, 'hapusBookingOperasi'])->name('rawat-jalan.hapus-booking-operasi');
    Route::post('/rawat-jalan/simpan-laporan-operasi', [RajalController::class, 'simpanLaporanOperasi'])->name('rawat-jalan.simpan-laporan-operasi');
    Route::delete('/rawat-jalan/hapus-laporan-operasi', [RajalController::class, 'hapusLaporanOperasi'])->name('rawat-jalan.hapus-laporan-operasi');
    Route::get('/rawat-jalan/cetak-laporan-operasi/{no_rawat?}', [RajalController::class, 'cetakLaporanOperasi'])->name('rawat-jalan.cetak-laporan-operasi');
    Route::get('/rawat-jalan/cetak-booking-operasi/{no_rawat?}', [RajalController::class, 'cetakBookingOperasi'])->name('rawat-jalan.cetak-booking-operasi');

    // Diagnosa & Prosedur Rajal (ICD-10 & ICD-9-CM)
    Route::get('/rawat-jalan/master-diagnosa', [RajalController::class, 'masterDiagnosa'])->name('rawat-jalan.master-diagnosa');
    Route::post('/rawat-jalan/simpan-diagnosa', [RajalController::class, 'simpanDiagnosa'])->name('rawat-jalan.simpan-diagnosa');
    Route::delete('/rawat-jalan/hapus-diagnosa', [RajalController::class, 'hapusDiagnosa'])->name('rawat-jalan.hapus-diagnosa');
    Route::get('/rawat-jalan/master-prosedur', [RajalController::class, 'masterProsedur'])->name('rawat-jalan.master-prosedur');
    Route::post('/rawat-jalan/simpan-prosedur', [RajalController::class, 'simpanProsedur'])->name('rawat-jalan.simpan-prosedur');
    Route::delete('/rawat-jalan/hapus-prosedur', [RajalController::class, 'hapusProsedur'])->name('rawat-jalan.hapus-prosedur');

    // Tindakan Rajal
    Route::get('/rawat-jalan/master-tindakan', [RajalController::class, 'masterTindakan'])->name('rawat-jalan.master-tindakan');
    Route::post('/rawat-jalan/simpan-tindakan', [RajalController::class, 'simpanTindakan'])->name('rawat-jalan.simpan-tindakan');
    Route::delete('/rawat-jalan/hapus-tindakan', [RajalController::class, 'hapusTindakan'])->name('rawat-jalan.hapus-tindakan');

    // Resume Medis Rajal
    Route::post('/rawat-jalan/simpan-resume', [RajalController::class, 'simpanResume'])->name('rawat-jalan.simpan-resume');
    Route::get('/rawat-jalan/template-resume', [RajalController::class, 'getTemplateResume'])->name('rawat-jalan.template-resume');
    Route::post('/rawat-jalan/simpan-template-resume', [RajalController::class, 'simpanTemplateResume'])->name('rawat-jalan.simpan-template-resume');
    Route::delete('/rawat-jalan/hapus-template-resume', [RajalController::class, 'hapusTemplateResume'])->name('rawat-jalan.hapus-template-resume');

    // ===================================================================
    // RAWAT INAP
    // ===================================================================
    Route::get('/rawat-inap', [RanapController::class, 'index'])->name('rawat-inap');

    // Detail pasien ranap (JSON)
    Route::get('/rawat-inap/pasien-detail/{no_rawat?}', [RanapController::class, 'getPasienDetail'])
        ->where('no_rawat', '.*')
        ->name('rawat-inap.pasien-detail');

    // SOAP / Catatan Perkembangan Harian Ranap
    Route::post('/rawat-inap/simpan-soap', [RanapController::class, 'simpanSoapRanap'])->name('rawat-inap.simpan-soap');
    Route::delete('/rawat-inap/hapus-soap', [RanapController::class, 'hapusSoapRanap'])->name('rawat-inap.hapus-soap');

    // Diagnosa ICD-10
    Route::get('/rawat-inap/master-diagnosa', [RanapController::class, 'masterDiagnosa'])->name('rawat-inap.master-diagnosa');
    Route::post('/rawat-inap/simpan-diagnosa', [RanapController::class, 'simpanDiagnosa'])->name('rawat-inap.simpan-diagnosa');
    Route::delete('/rawat-inap/hapus-diagnosa', [RanapController::class, 'hapusDiagnosa'])->name('rawat-inap.hapus-diagnosa');

    // Tindakan Rawat Inap
    Route::get('/rawat-inap/master-tindakan', [RanapController::class, 'masterTindakanRanap'])->name('rawat-inap.master-tindakan');
    Route::post('/rawat-inap/simpan-tindakan', [RanapController::class, 'simpanTindakan'])->name('rawat-inap.simpan-tindakan');
    Route::delete('/rawat-inap/hapus-tindakan', [RanapController::class, 'hapusTindakan'])->name('rawat-inap.hapus-tindakan');

    // Awal Medis Ranap
    Route::post('/rawat-inap/simpan-awal-medis', [RanapController::class, 'simpanAwalMedisRanap'])->name('rawat-inap.simpan-awal-medis');

    // Resep Obat Ranap
    Route::get('/rawat-inap/master-obat', [RanapController::class, 'masterObatRanap'])->name('rawat-inap.master-obat');
    Route::get('/rawat-inap/master-aturan-pakai', [RanapController::class, 'masterAturanPakai'])->name('rawat-inap.master-aturan-pakai');
    Route::post('/rawat-inap/simpan-resep', [RanapController::class, 'simpanResepRanap'])->name('rawat-inap.simpan-resep');
    Route::delete('/rawat-inap/hapus-resep', [RanapController::class, 'hapusResep'])->name('rawat-inap.hapus-resep');

    // Lab Ranap
    Route::get('/rawat-inap/master-lab', [RanapController::class, 'masterLabRanap'])->name('rawat-inap.master-lab');
    Route::post('/rawat-inap/simpan-lab', [RanapController::class, 'simpanLabRanap'])->name('rawat-inap.simpan-lab');

    // Radiologi Ranap
    Route::get('/rawat-inap/master-radiologi', [RanapController::class, 'masterRadiologiRanap'])->name('rawat-inap.master-radiologi');
    Route::post('/rawat-inap/simpan-radiologi', [RanapController::class, 'simpanRadiologiRanap'])->name('rawat-inap.simpan-radiologi');

    // Operasi (Jadwal & Laporan Operasi) Ranap
    Route::get('/rawat-inap/master-operasi', [RanapController::class, 'masterOperasi'])->name('rawat-inap.master-operasi');
    Route::get('/rawat-inap/master-dokter', [RanapController::class, 'masterDokter'])->name('rawat-inap.master-dokter');
    Route::get('/rawat-inap/master-ruang-ok', [RanapController::class, 'masterRuangOk'])->name('rawat-inap.master-ruang-ok');
    Route::get('/rawat-inap/master-template-laporan-operasi', [RanapController::class, 'masterTemplateLaporanOperasi'])->name('rawat-inap.master-template-laporan-operasi');
    Route::post('/rawat-inap/simpan-booking-operasi', [RanapController::class, 'simpanBookingOperasi'])->name('rawat-inap.simpan-booking-operasi');
    Route::delete('/rawat-inap/hapus-booking-operasi', [RanapController::class, 'hapusBookingOperasi'])->name('rawat-inap.hapus-booking-operasi');
    Route::post('/rawat-inap/simpan-laporan-operasi', [RanapController::class, 'simpanLaporanOperasi'])->name('rawat-inap.simpan-laporan-operasi');
    Route::delete('/rawat-inap/hapus-laporan-operasi', [RanapController::class, 'hapusLaporanOperasi'])->name('rawat-inap.hapus-laporan-operasi');
    Route::get('/rawat-inap/cetak-laporan-operasi/{no_rawat?}', [RanapController::class, 'cetakLaporanOperasi'])->name('rawat-inap.cetak-laporan-operasi');
    Route::get('/rawat-inap/cetak-booking-operasi/{no_rawat?}', [RanapController::class, 'cetakBookingOperasi'])->name('rawat-inap.cetak-booking-operasi');

    // Resume Medis Ranap
    Route::post('/rawat-inap/simpan-resume', [RanapController::class, 'simpanResumeRanap'])->name('rawat-inap.simpan-resume');
    Route::get('/rawat-inap/template-resume', [RanapController::class, 'getTemplateResume'])->name('rawat-inap.template-resume');
    Route::post('/rawat-inap/simpan-template-resume', [RanapController::class, 'simpanTemplateResume'])->name('rawat-inap.simpan-template-resume');
    Route::delete('/rawat-inap/hapus-template-resume', [RanapController::class, 'hapusTemplateResume'])->name('rawat-inap.hapus-template-resume');
});

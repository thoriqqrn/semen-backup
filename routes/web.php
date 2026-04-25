<?php

use Illuminate\Support\Facades\Route;

// == Controller Frontend ==
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\GaleriController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\CekStatusController;
use App\Http\Controllers\DependentDropdownController;

// == Controller Admin ==
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GaleriController as AdminGaleriController;
use App\Http\Controllers\Admin\ArtikelController as AdminArtikelController;
use App\Http\Controllers\Admin\PendaftarController as AdminPendaftarController;
use App\Http\Controllers\Admin\PesertaHajiController as AdminPesertaHajiController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\WilayahController as AdminWilayahController;
use App\Http\Controllers\Admin\OcrController as AdminOcrController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ========================================================
// 1. FRONTEND
// ========================================================

Route::get('/', function () { return view('beranda'); })->name('beranda');
Route::get('/tentang-kami', function () { return view('tentangkami'); })->name('tentang-kami');
Route::get('/program', function () { return view('program'); })->name('program');

Route::get('/galeri', [GaleriController::class, 'index'])->name('galeri');
Route::get('/berita', [BeritaController::class, 'index'])->name('berita');
Route::get('/berita/{artikel:slug}', [BeritaController::class, 'show'])->name('berita.show');

// Pendaftaran
Route::get('/pendaftaran', [PendaftaranController::class, 'create'])->name('pendaftaran.form');
Route::post('/pendaftaran', [PendaftaranController::class, 'store'])->name('pendaftaran.store');
Route::get('/pendaftaran-sukses', [PendaftaranController::class, 'success'])->name('pendaftaran.sukses');
Route::get('/pendaftaran/download-kartu/{kode}', [PendaftaranController::class, 'downloadKartu'])->name('pendaftaran.download');

// Cek Status (AJAX/Script)
Route::get('/cek-status', [CekStatusController::class, 'index'])->name('cek-status');
Route::get('/api/pendaftar-data', [CekStatusController::class, 'getData'])->name('api.pendaftar.data');

// API Helper
Route::get('api/kecamatan', [DependentDropdownController::class, 'getKecamatan'])->name('api.kecamatan');
Route::get('api/kelurahan', [DependentDropdownController::class, 'getKelurahan'])->name('api.kelurahan');

// ========================================================
// 2. AUTH
// ========================================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ========================================================
// 3. ADMIN BACKEND
// ========================================================

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('galeri', AdminGaleriController::class)->names('galeri');
    Route::resource('artikel', AdminArtikelController::class)->names('artikel');

    // == PENDAFTAR ==
    Route::get('/pendaftar', [AdminPendaftarController::class, 'index'])->name('pendaftar.index');
    Route::get('/pendaftar/{pendaftar}', [AdminPendaftarController::class, 'show'])->name('pendaftar.show');
    Route::put('/pendaftar/{pendaftar}', [AdminPendaftarController::class, 'update'])->name('pendaftar.update');

    // == EXPORT ==
    Route::get('/pendaftar/export/excel', [AdminPendaftarController::class, 'exportExcel'])->name('pendaftar.export.excel');
    Route::get('/pendaftar/export/pdf', [AdminPendaftarController::class, 'exportPdf'])->name('pendaftar.export.pdf');

    // == PESERTA DITERIMA ==
    Route::get('/jamaah-diterima', [AdminPesertaHajiController::class, 'index'])->name('jamaah-diterima');
    Route::get('/peserta-haji', [AdminPesertaHajiController::class, 'index'])->name('peserta-haji.index');

    // == ROUTE YANG MEMPERBAIKI ERROR ANDA (Perhatikan strip '-') ==
    // View Anda memanggil: admin.pendaftar.verifikasi-terima
    // Maka di sini namenya harus: pendaftar.verifikasi-terima (karena prefix admin.)
    Route::post('/pendaftar/{pendaftar}/verifikasi-terima', [AdminPendaftarController::class, 'verifikasiTerima'])
        ->name('pendaftar.verifikasi-terima'); 
        
    Route::post('/pendaftar/{pendaftar}/verifikasi-tolak', [AdminPendaftarController::class, 'verifikasiTolak'])
        ->name('pendaftar.verifikasi-tolak');

    Route::post('/pendaftar/{pendaftar}/ubah-status', [AdminPendaftarController::class, 'ubahStatus'])
        ->name('pendaftar.ubah-status');

    // == OCR ==
    Route::post('/pendaftar/{pendaftar}/process-ocr-ktp', [AdminOcrController::class, 'processKtp'])->name('pendaftar.ocr.ktp');
    Route::post('/pendaftar/{pendaftar}/process-ocr-kk', [AdminOcrController::class, 'processKk'])->name('pendaftar.ocr.kk');
    Route::get('/test-gemini', [AdminOcrController::class, 'testGemini'])->name('test.gemini');

    // == SETTINGS & WILAYAH ==
    Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
    Route::get('/wilayah', [AdminWilayahController::class, 'index'])->name('wilayah.index');
    Route::put('/wilayah', [AdminWilayahController::class, 'update'])->name('wilayah.update');
    Route::post('/wilayah/kecamatan', [AdminWilayahController::class, 'storeKecamatan'])->name('wilayah.kecamatan.store');
    Route::put('/wilayah/kecamatan/{kecamatan}', [AdminWilayahController::class, 'updateKecamatan'])->name('wilayah.kecamatan.update');
    Route::delete('/wilayah/kecamatan/{kecamatan}', [AdminWilayahController::class, 'destroyKecamatan'])->name('wilayah.kecamatan.destroy');
    Route::post('/wilayah/kelurahan', [AdminWilayahController::class, 'storeKelurahan'])->name('wilayah.kelurahan.store');
    Route::put('/wilayah/kelurahan/{kelurahan}', [AdminWilayahController::class, 'updateKelurahan'])->name('wilayah.kelurahan.update');
    Route::delete('/wilayah/kelurahan/{kelurahan}', [AdminWilayahController::class, 'destroyKelurahan'])->name('wilayah.kelurahan.destroy');
});
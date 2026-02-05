<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\PembayaranController;
use App\Http\Controllers\Admin\ManajemenSiswaController;
use App\Http\Controllers\Pengajar\PengajarDashboardController;
use App\Http\Controllers\Pengajar\MateriController;
use App\Http\Controllers\Pengajar\TryoutController;

/*
|--------------------------------------------------------------------------
| Web Routes - Spekta Academy
|--------------------------------------------------------------------------
*/

// --- 1. JALUR AUTHENTICATION ---
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [WebAuthController::class, 'showLogin'])->name('login');
Route::post('/login', [WebAuthController::class, 'login']);
Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');


// --- 2. GROUP ADMINISTRASI (Role: admin, Prefix: /admin) ---
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard & Informasi Utama
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/galeri', [AdminDashboardController::class, 'galeri'])->name('galeri');
    Route::get('/pengumuman', [AdminDashboardController::class, 'pengumuman'])->name('pengumuman');

    // Manajemen Siswa & Kelas
    Route::get('/siswa', [ManajemenSiswaController::class, 'index'])->name('siswa.index');
    Route::get('/siswa/kelas', [ManajemenSiswaController::class, 'kelolaKelas'])->name('siswa.kelas');

    // Manajemen Keuangan & Marketing
    Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
    Route::post('/pembayaran/verifikasi/{id}', [PembayaranController::class, 'verifikasi'])->name('pembayaran.verify');
    Route::get('/promo', [PembayaranController::class, 'promo'])->name('promo');
});


// --- 3. GROUP PENGAJAR (Role: pengajar, Prefix: /pengajar) ---
Route::middleware(['auth', 'role:pengajar'])->prefix('pengajar')->name('pengajar.')->group(function () {

    // Dashboard & Kehadiran
    Route::get('/dashboard', [PengajarDashboardController::class, 'index'])->name('dashboard');
    Route::get('/absensi', [PengajarDashboardController::class, 'absensi'])->name('absensi');

    // Manajemen Pembelajaran
    Route::get('/materi', [MateriController::class, 'index'])->name('materi.index');
    Route::post('/materi/upload', [MateriController::class, 'store'])->name('materi.store');

    // Evaluasi & Tryout
    Route::get('/soal-tryout', [TryoutController::class, 'buatSoal'])->name('tryout.create');
    Route::get('/nilai', [TryoutController::class, 'lihatNilai'])->name('tryout.nilai');
});

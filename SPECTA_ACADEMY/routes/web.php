<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\GaleriController;
use App\Http\Controllers\Admin\PembayaranController;
use App\Http\Controllers\Admin\ManajemenSiswaController;
use App\Http\Controllers\Admin\PengumumanController;
use App\Http\Controllers\Pengajar\PengajarDashboardController;
use App\Http\Controllers\Pengajar\MateriController;
use App\Http\Controllers\Pengajar\TryoutController;

/*
|--------------------------------------------------------------------------
| Web Routes - Spekta Academy
|--------------------------------------------------------------------------
*/

// --- JALUR UTAMA & LOGIN ---
Route::get('/', function () { return redirect()->route('login'); });
Route::get('/login', [WebAuthController::class, 'showLogin'])->name('login');
Route::post('/login', [WebAuthController::class, 'login']);
Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');


// --- 1. GROUP ADMINISTRASI (Role: Admin) ---
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard Utama
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Manajemen Pengumuman
    // Route::get('/pengumuman', [PengumumanController::class, 'index'])->name('pengumuman.index');
    // Route::post('/pengumuman', [PengumumanController::class, 'store'])->name('pengumuman.store');
    // Route::delete('/pengumuman/{id}', [PengumumanController::class, 'destroy'])->name('pengumuman.destroy');

    // Manajemen Galeri
    Route::get('/galeri', [GaleriController::class, 'index'])->name('galeri.index');
    Route::post('/galeri', [GaleriController::class, 'store'])->name('galeri.store');
    Route::get('/galeri/edit/{id}', [GaleriController::class, 'edit'])->name('galeri.edit');
    Route::put('/galeri/update/{id}', [GaleriController::class, 'update'])->name('galeri.update');
    Route::delete('/galeri/hapus/{id}', [GaleriController::class, 'destroy'])->name('galeri.destroy');

    // Manajemen Siswa & Verifikasi Pendaftaran Kelas (ALUR INTI)
    Route::get('/siswa', [ManajemenSiswaController::class, 'index'])->name('siswa.index');
    Route::get('/pendaftaran', [ManajemenSiswaController::class, 'indexPendaftaran'])->name('pendaftaran.index');
    Route::get('/pendaftaran/{id}', [ManajemenSiswaController::class, 'formAktivasi'])->name('pendaftaran.show');
    Route::post('/pendaftaran/aktivasi/{id}', [ManajemenSiswaController::class, 'prosesAktivasi'])->name('pendaftaran.aktivasi');

    // Manajemen Keuangan & Promo
    Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
    Route::post('/pembayaran/verifikasi/{id}', [PembayaranController::class, 'verifikasi'])->name('pembayaran.verify');
    Route::get('/promo', [PembayaranController::class, 'promo'])->name('promo');
});


// --- 2. GROUP PENGAJAR (Role: Pengajar) ---
Route::middleware(['auth', 'role:pengajar'])->prefix('pengajar')->name('pengajar.')->group(function () {

    // Dashboard Pengajar
    Route::get('/dashboard', [PengajarDashboardController::class, 'index'])->name('dashboard');

    // Absensi Per Kelas (Dinamis)
    Route::get('/absensi', [PengajarDashboardController::class, 'absensi'])->name('absensi.index');
    Route::get('/absensi/{class_id}', [PengajarDashboardController::class, 'showAbsensi'])->name('absensi.show');

    // Manajemen Pembelajaran
    Route::get('/materi', [MateriController::class, 'index'])->name('materi.index');
    Route::post('/materi/upload', [MateriController::class, 'store'])->name('materi.store');

    // Evaluasi
    Route::get('/soal-tryout', [TryoutController::class, 'buatSoal'])->name('tryout.create');
    Route::get('/nilai', [TryoutController::class, 'lihatNilai'])->name('tryout.nilai');
});

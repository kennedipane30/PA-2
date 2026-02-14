<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\GaleriController;
use App\Http\Controllers\Admin\PembayaranController;
use App\Http\Controllers\Admin\ManajemenSiswaController;
use App\Http\Controllers\Admin\PengumumanController; // 1. Pastikan ini dipanggil
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

    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // 2. Perbaikan: Menggunakan PengumumanController (bukan AdminDashboardController)
    // Route::get('/pengumuman', [PengumumanController::class, 'index'])->name('pengumuman.index');
    // Route::post('/pengumuman', [PengumumanController::class, 'store'])->name('pengumuman.store');
    // Route::delete('/pengumuman/{id}', [PengumumanController::class, 'destroy'])->name('pengumuman.destroy');

    // MANAJEMEN GALERI
    Route::get('/galeri', [GaleriController::class, 'index'])->name('galeri.index');
    Route::post('/galeri', [GaleriController::class, 'store'])->name('galeri.store');
    Route::get('/galeri/edit/{id}', [GaleriController::class, 'edit'])->name('galeri.edit'); // Rute Edit
    Route::put('/galeri/update/{id}', [GaleriController::class, 'update'])->name('galeri.update'); // Rute Update
    Route::delete('/galeri/hapus/{id}', [GaleriController::class, 'destroy'])->name('galeri.destroy'); // Rute Hapus

    // Manajemen Siswa & Verifikasi Pendaftaran Kelas (Fitur Inti yang tadi dibahas)
    // Route::get('/siswa', [ManajemenSiswaController::class, 'index'])->name('siswa.index');
    // Route::get('/pendaftaran-kelas', [ManajemenSiswaController::class, 'pendaftaranKelas'])->name('siswa.pendaftaran');
    // Route::post('/pendaftaran-kelas/aktivasi/{id}', [ManajemenSiswaController::class, 'aktivasiSiswa'])->name('siswa.aktivasi');

    // Manajemen Keuangan
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

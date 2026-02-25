<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\GaleriController;
use App\Http\Controllers\Admin\PembayaranController;
use App\Http\Controllers\Admin\ManajemenSiswaController;
use App\Http\Controllers\Admin\PengumumanController;
use App\Http\Controllers\Admin\JadwalController;
use App\Http\Controllers\Admin\ManajemenPengajarController;
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

    // Manajemen Pengumuman & Galeri
    // Route::get('/pengumuman', [PengumumanController::class, 'index'])->name('pengumuman.index');
    // Route::post('/pengumuman', [PengumumanController::class, 'store'])->name('pengumuman.store');
    // Route::delete('/pengumuman/{id}', [PengumumanController::class, 'destroy'])->name('pengumuman.destroy');

    Route::get('/galeri', [GaleriController::class, 'index'])->name('galeri.index');
    Route::post('/galeri', [GaleriController::class, 'store'])->name('galeri.store');
    Route::get('/galeri/edit/{id}', [GaleriController::class, 'edit'])->name('galeri.edit');
    Route::put('/galeri/update/{id}', [GaleriController::class, 'update'])->name('galeri.update');
    Route::delete('/galeri/hapus/{id}', [GaleriController::class, 'destroy'])->name('galeri.destroy');

    // Manajemen Jadwal & Akun Pengajar
    Route::resource('jadwal', JadwalController::class);
    Route::resource('manajemen-pengajar', ManajemenPengajarController::class);

    // Manajemen Siswa (Hierarkis)
    Route::prefix('siswa')->name('siswa.')->group(function () {
        Route::get('/semua', [ManajemenSiswaController::class, 'index'])->name('index');
        Route::get('/tambah-kelas', [ManajemenSiswaController::class, 'indexPendaftaran'])->name('pendaftaran');
        Route::get('/tambah-kelas/aktivasi/{id}', [ManajemenSiswaController::class, 'formAktivasi'])->name('form_aktivasi');
        Route::post('/tambah-kelas/proses/{id}', [ManajemenSiswaController::class, 'prosesAktivasi'])->name('proses_aktivasi');
    });

    // Keuangan & Promo
    Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
    Route::post('/pembayaran/verifikasi/{id}', [PembayaranController::class, 'verifikasi'])->name('pembayaran.verify');
    Route::get('/promo', [PembayaranController::class, 'promo'])->name('promo');
});


// --- 2. GROUP PENGAJAR (Role: Pengajar) ---
Route::middleware(['auth', 'role:pengajar'])->prefix('pengajar')->name('pengajar.')->group(function () {

    // Dashboard & Jadwal
    Route::get('/dashboard', [PengajarDashboardController::class, 'index'])->name('dashboard');
    Route::get('/jadwal-mengajar', [PengajarDashboardController::class, 'jadwalSaya'])->name('jadwal.index');

    // Absensi Per Kelas (Dinamis)
    Route::get('/absensi', [PengajarDashboardController::class, 'absensi'])->name('absensi.index');
    Route::get('/absensi/{class_id}', [PengajarDashboardController::class, 'showAbsensi'])->name('absensi.show');

    // MODIFIKASI: Menambahkan rute simpan absensi (POST)
    Route::post('/absensi/simpan', [PengajarDashboardController::class, 'storeAbsensi'])->name('absensi.store');

    // Materi & Evaluasi
    Route::get('/materi', [MateriController::class, 'index'])->name('materi.index');
    Route::post('/materi/upload', [MateriController::class, 'store'])->name('materi.store');
    Route::get('/soal-tryout', [TryoutController::class, 'buatSoal'])->name('tryout.create');
    Route::get('/nilai', [TryoutController::class, 'lihatNilai'])->name('tryout.nilai');
});

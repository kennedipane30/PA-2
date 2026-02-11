<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\GaleriController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Specta Academy
|--------------------------------------------------------------------------
*/

// --- 1. ROUTE PUBLIC (Bisa diakses sebelum login) ---

// Autentikasi
Route::post('/register', [AuthController::class, 'registerSiswa']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);

// Galeri (Sudah terfilter 14 hari di Controller)
Route::get('/galeri', [GaleriController::class, 'apiIndex']);

// Ambil Daftar 4 Program Spekta (Untuk ditampilkan di Home Flutter)
// Route::get('/programs', [ClassController::class, 'index']);


// --- 2. ROUTE PROTECTED (Wajib Login / Bawa Token Sanctum) ---

Route::middleware('auth:sanctum')->group(function () {

    // Ambil data profil lengkap siswa yang login
    Route::get('/user', function (Request $request) {
        return $request->user()->load('role', 'profile');
    });

    Route::post('/logout', [AuthController::class, 'logout']);


    // --- 3. ROUTE KHUSUS SISWA (Akses Fitur Akademik) ---
    Route::middleware('role:siswa')->group(function () {

        // Cek apakah siswa sudah punya akses/aktif di kelas tertentu
        Route::post('/class/check-status', [AuthController::class, 'checkClassStatus']);

        // Siswa klik tombol "Daftar Sekarang" di Mobile
        Route::post('/class/join', [AuthController::class, 'joinClass']);

        // Nanti di sini kita tambah route:
        // Route::get('/materi/{class_id}', [MateriController::class, 'apiIndex']);
    });

});

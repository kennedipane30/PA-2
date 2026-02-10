<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\GaleriController; // 1. Tambahkan Import ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Specta Academy
|--------------------------------------------------------------------------
*/

// --- 1. ROUTE PUBLIC (Bisa diakses tanpa login) ---

// Registrasi Siswa baru
Route::post('/register', [AuthController::class, 'registerSiswa']);

// Login Step 1: Cek Email & Pass, lalu kirim OTP
Route::post('/login', [AuthController::class, 'login']);

// Login Step 2: Verifikasi OTP untuk mendapatkan Token
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);

// Galeri: Agar siswa bisa melihat foto kegiatan di Beranda Mobile tanpa login
Route::get('/galeri', [GaleriController::class, 'apiIndex']);


// --- 2. ROUTE PROTECTED (Wajib bawa Token / auth:sanctum) ---

Route::middleware('auth:sanctum')->group(function () {

    // Ambil data user yang sedang login beserta Role dan Profilnya
    Route::get('/user', function (Request $request) {
        return $request->user()->load('role', 'profile');
    });

    // Logout: Menghapus Token yang sedang digunakan
    Route::post('/logout', [AuthController::class, 'logout']);


    // --- 3. ROUTE KHUSUS ROLE ---

    // Group khusus Siswa (Akses dari Mobile)
    Route::middleware('role:siswa')->group(function () {
        // Nanti di sini kita tambah route pendaftaran kelas & tryout
    });

    // Group khusus Admin (Jika sewaktu-waktu ada fitur admin di mobile)
    Route::middleware('role:admin')->group(function () {
        // Route::get('/admin/stats', [AdminController::class, 'stats']);
    });

});

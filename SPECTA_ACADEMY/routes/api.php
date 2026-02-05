<?php

use App\Http\Controllers\AuthController;
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


// --- 2. ROUTE PROTECTED (Wajib bawa Token / auth:sanctum) ---

Route::middleware('auth:sanctum')->group(function () {

    // Ambil data user yang sedang login beserta Role dan Profilnya
    Route::get('/user', function (Request $request) {
        return $request->user()->load('role', 'profile');
    });

    // Logout: Menghapus Token yang sedang digunakan
    Route::post('/logout', [AuthController::class, 'logout']);


    // --- 3. ROUTE KHUSUS ROLE (Opsional - Jika sudah setup Middleware Role) ---

    // Contoh Group khusus Admin
    Route::middleware('role:admin')->group(function () {
        // Route::get('/admin/stats', [AdminController::class, 'stats']);
    });

    // Contoh Group khusus Siswa
    Route::middleware('role:siswa')->group(function () {
        // Route::get('/siswa/my-courses', [CourseController::class, 'myCourses']);
    });

});

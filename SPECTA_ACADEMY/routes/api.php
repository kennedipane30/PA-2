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

// STEP 1: Registrasi Siswa Baru (Menerima Nama, Email, WA, Pass, Konf Pass)
Route::post('/register', [AuthController::class, 'registerSiswa']);

// STEP 2: Verifikasi OTP setelah pendaftaran (Aktivasi Akun)
Route::post('/verify-registration', [AuthController::class, 'verifyRegistration']);

// STEP 3: Login (Hanya Nama dan Password - Akun harus is_verified = true)
Route::post('/login', [AuthController::class, 'login']);


// Galeri Spekta (Data difilter 14 hari di Controller)
Route::get('/galeri', [GaleriController::class, 'apiIndex']);


// --- 2. ROUTE PROTECTED (Wajib bawa Token / auth:sanctum) ---

Route::middleware('auth:sanctum')->group(function () {

    // Ambil data profil lengkap siswa yang login (Gunakan student sesuai ERD)
    Route::get('/user', function (Request $request) {
        return $request->user()->load('role', 'student');
    });

    // Logout: Menghapus Token
    Route::post('/logout', [AuthController::class, 'logout']);


    // --- 3. ROUTE KHUSUS SISWA (Akses Fitur Akademik) ---
    Route::middleware('role:siswa')->group(function () {

        // Cek status akses kelas (none/pending/aktif)
        Route::post('/class/check-status', [AuthController::class, 'checkClassStatus']);

        // Pendaftaran Kelas
        Route::post('/class/join', [AuthController::class, 'joinClass']);

    });

        Route::middleware('auth:sanctum')->group(function () {
        // ... rute lain ...
        Route::post('/update-profile', [AuthController::class, 'updateProfile']);
    });

});

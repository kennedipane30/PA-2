<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\GaleriController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Specta Academy (Untuk Mobile Flutter)
|--------------------------------------------------------------------------
*/

// --- 1. JALUR PUBLIC (Bisa diakses tanpa login) ---
Route::post('/register', [AuthController::class, 'registerSiswa']);
Route::post('/verify-registration', [AuthController::class, 'verifyRegistration']);
Route::post('/login', [AuthController::class, 'login']);

// Galeri (Dapat dilihat siswa di Beranda)
Route::get('/galeri', [GaleriController::class, 'apiIndex']);


// --- 2. JALUR PROTECTED (Wajib bawa Token / auth:sanctum) ---
Route::middleware('auth:sanctum')->group(function () {

    // Ambil data profil lengkap (Eager Load Student sesuai ERD)
    Route::get('/user', function (Request $request) {
        return $request->user()->load('role', 'student');
    });

    // Melengkapi Data Diri (Nama Ortu, Alamat, NISN, Tgl Lahir)
    Route::post('/update-profile', [AuthController::class, 'updateProfile']);

    Route::post('/logout', [AuthController::class, 'logout']);


    // --- 3. KHUSUS ROLE SISWA ---
    Route::middleware('role:siswa')->group(function () {

        // Mengambil materi & tryout unik per-Kelas
        Route::post('/class/content', [AuthController::class, 'getClassContent']);

        // Cek status pendaftaran (none/pending/aktif/expired)
        Route::post('/class/check-status', [AuthController::class, 'checkClassStatus']);

        // Siswa klik Daftar & Upload Bukti Transfer (Multipart)
        Route::post('/class/join', [AuthController::class, 'joinClass']);

    });

});

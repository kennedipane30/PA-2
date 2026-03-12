<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController; 
use App\Http\Controllers\Admin\GaleriController;

/*
|--------------------------------------------------------------------------
| API Routes - Specta Academy (Mobile)
|--------------------------------------------------------------------------
*/

// --- 1. PUBLIC ROUTES (Akses Tanpa Token/Login) ---

// Registrasi Siswa (Memicu pengiriman OTP ke Email)
Route::post('/register', [AuthController::class, 'registerSiswa']); 

// PERBAIKAN: Nama rute disamakan dengan pemanggilan di Flutter (/verify-registration)
// Dan nama fungsi disamakan dengan yang ada di AuthController (verifyRegistration)
Route::post('/verify-registration', [AuthController::class, 'verifyRegistration']); 

// Login
Route::post('/login', [AuthController::class, 'login']);

// Galeri (Untuk Fitur Berita/Aktivitas di Mobile)
Route::get('/galeri', [GaleriController::class, 'getApi']); 


// --- JALUR LUPA PASSWORD ---
// 1. Kirim Email untuk mendapatkan kode OTP reset
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
// 2. Kirim OTP dan Password Baru untuk proses Reset
Route::post('/reset-password', [AuthController::class, 'resetPassword']);



// --- 2. PROTECTED ROUTES (Hanya bisa diakses jika sudah Login/Punya Token) ---
Route::middleware('auth:sanctum')->group(function () {

    // Ambil Data Profil User & Role yang sedang login
    Route::get('/user', function (Request $request) {
        return $request->user()->load('role', 'student');
    });

    Route::post('/update-profile', [AuthController::class, 'updateProfile']);
    Route::post('/logout', [AuthController::class, 'logout']);


    // --- 3. KHUSUS ROLE SISWA (Diproteksi Middleware Role) ---
    Route::middleware('role:siswa')->group(function () {

        // Manajemen Kelas & Pembelian
        Route::post('/class/content', [AuthController::class, 'getClassContent']);
        Route::post('/class/check-status', [AuthController::class, 'checkClassStatus']);
        Route::post('/class/join', [AuthController::class, 'joinClass']);
        
        // Jadwal Belajar Siswa
        Route::get('/schedules', [AuthController::class, 'getSiswaSchedule']);

        // Tryout & Evaluasi
        Route::post('/tryout/questions', [AuthController::class, 'getQuestions']);
        Route::post('/tryout/submit', [AuthController::class, 'submitTryout']);
        Route::get('/tryout/download/{id}', [AuthController::class, 'downloadPembahasan']);
    });
});
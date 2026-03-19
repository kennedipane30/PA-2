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

// Verifikasi Pendaftaran
Route::post('/verify-registration', [AuthController::class, 'verifyRegistration']); 

// Login
Route::post('/login', [AuthController::class, 'login']);

// Galeri (Untuk Fitur Berita/Aktivitas di Mobile)
Route::get('/galeri', [GaleriController::class, 'getApi']); 


// --- JALUR LUPA PASSWORD ---
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);



// --- 2. PROTECTED ROUTES (Harus Login / Pakai Token) ---
Route::middleware('auth:sanctum')->group(function () {

    // Ambil Data Profil User & Role yang sedang login
    Route::get('/user', function (Request $request) {
        return $request->user()->load('role', 'student');
    });

    Route::post('/update-profile', [AuthController::class, 'updateProfile']);
    Route::post('/logout', [AuthController::class, 'logout']);


    // --- 3. KHUSUS ROLE SISWA ---
    Route::middleware('role:siswa')->group(function () {
        Route::post('/class/content', [AuthController::class, 'getClassContent']);
        Route::post('/class/check-status', [AuthController::class, 'checkClassStatus']);
        Route::post('/class/join', [AuthController::class, 'joinClass']);
        Route::get('/schedules', [AuthController::class, 'getSiswaSchedule']);
        Route::post('/tryout/questions', [AuthController::class, 'getQuestions']);
        Route::post('/tryout/submit', [AuthController::class, 'submitTryout']);
        Route::get('/tryout/download/{id}', [AuthController::class, 'downloadPembahasan']);
    });


    // --- 4. KHUSUS ROLE ADMIN (Manajemen User) ---
    // Fitur ini memungkinkan Admin menghapus user langsung dari aplikasi
    Route::middleware('role:admin')->group(function () {
        // Mendapatkan semua daftar user untuk ditampilkan di Dashboard Admin
        Route::get('/admin/users', [AuthController::class, 'getAllUsers']);
        
        // Menghapus user berdasarkan ID (usersID sesuai primary key Anda)
        Route::delete('/admin/users/{usersID}', [AuthController::class, 'deleteUser']);
    });

});
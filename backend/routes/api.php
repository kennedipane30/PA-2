<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController; 
use App\Http\Controllers\Admin\GaleriController;
use App\Http\Controllers\Admin\PembayaranController;
use App\Http\Controllers\Api\MidtransController; // <--- IMPORT CONTROLLER MIDTRANS

/*
|--------------------------------------------------------------------------
| API Routes - Specta Academy (Mobile)
|--------------------------------------------------------------------------
*/

// --- 1. PUBLIC ROUTES (Tanpa Login) ---
Route::post('/register', [AuthController::class, 'registerSiswa']); 
Route::post('/verify-registration', [AuthController::class, 'verifyRegistration']); 
Route::post('/login', [AuthController::class, 'login']);
Route::get('/galeri', [GaleriController::class, 'getApi']); 
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

/**
 * WEBHOOK MIDTRANS (Sangat Penting)
 * Jalur ini harus Public agar server Midtrans bisa mengirim data laporan 
 * pembayaran otomatis ke website Anda tanpa terhalang sistem login.
 */
Route::post('/midtrans/callback', [MidtransController::class, 'callback']);


// --- 2. PROTECTED ROUTES (Harus Login / Token Sanctum) ---
Route::middleware('auth:sanctum')->group(function () {

    // Profil User
    Route::get('/user', function (Request $request) {
        return $request->user()->load('role', 'student');
    });

    // Update Profil & Logout
    Route::post('/update-profile', [AuthController::class, 'updateProfile']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // --- FITUR UMUM (Bisa diakses Admin & Siswa) ---
    Route::post('/admin/promo/check', [PembayaranController::class, 'checkPromo']);


    // --- 3. KHUSUS ROLE SISWA ---
    Route::middleware('role:siswa')->group(function () {
        
        // FITUR PEMBAYARAN MIDTRANS (Tanpa Bukti Transaksi)
        // Siswa memanggil ini untuk mendapatkan Snap Token
        Route::post('/midtrans/token', [MidtransController::class, 'getSnapToken']);

        Route::post('/class/content', [AuthController::class, 'getClassContent']);
        Route::post('/class/check-status', [AuthController::class, 'checkClassStatus']);
        Route::post('/class/join', [AuthController::class, 'joinClass']);
        Route::get('/schedules', [AuthController::class, 'getSiswaSchedule']);
        Route::post('/tryout/questions', [AuthController::class, 'getQuestions']);
        Route::post('/tryout/submit', [AuthController::class, 'submitTryout']);
        Route::get('/tryout/download/{id}', [AuthController::class, 'downloadPembahasan']);
    });


    // --- 4. KHUSUS ROLE ADMIN ---
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/users', [AuthController::class, 'getAllUsers']);
        Route::delete('/admin/users/{user_id}', [AuthController::class, 'deleteUser']);
    });

});
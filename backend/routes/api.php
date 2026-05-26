<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController; 
use App\Http\Controllers\Admin\GaleriController;
use App\Http\Controllers\Admin\PembayaranController;
use App\Http\Controllers\Admin\ProgramController; 
use App\Http\Controllers\PaymentController; 
use App\Http\Controllers\Api\MidtransController;

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
Route::get('/programs', [ProgramController::class, 'getApi']); 

/**
 * WEBHOOK MIDTRANS (PENTING: Harus Public agar bisa dipanggil Midtrans dari Internet)
 */
Route::post('/midtrans/callback', [MidtransController::class, 'callback']);


// --- 2. PROTECTED ROUTES (Harus Login / Token Sanctum) ---
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user()->load('role', 'student');
    });

    Route::post('/update-profile', [AuthController::class, 'updateProfile']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/admin/promo/check', [PembayaranController::class, 'checkPromo']);
    Route::get('/programs/{id}', [ProgramController::class, 'showApi']);


    // --- 3. KHUSUS ROLE SISWA ---
    Route::middleware('role:siswa')->group(function () {
        
        // PEMBAYARAN MIDTRANS
        Route::post('/midtrans/token', [PaymentController::class, 'getSnapToken']);
        
        // --- TAMBAHKAN INI UNTUK CEK STATUS ENROLLMENT ---
        Route::post('/midtrans/check-status', [MidtransController::class, 'checkStatus']);
        // ------------------------------------------------
        
        Route::post('/midtrans/verify', [PaymentController::class, 'verifyPayment']);

        // KELAS & MATERI
        Route::post('/class/content', [AuthController::class, 'getClassContent']);
        Route::post('/class/check-status', [AuthController::class, 'checkClassStatus']);
        Route::post('/class/join', [AuthController::class, 'joinClass']);
        
        // JADWAL & TRYOUT
        Route::get('/schedules', [AuthController::class, 'getSiswaSchedule']);
        Route::post('/tryout/questions', [AuthController::class, 'getQuestions']);
        Route::post('/tryout/submit', [AuthController::class, 'submitTryout']);
        Route::get('/tryout/download/{id}', [AuthController::class, 'downloadPembahasan']);
    });


    // --- 4. KHUSUS ROLE ADMIN ---
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/users', [AuthController::class, 'getAllUsers']);
        Route::delete('/admin/users/{user_id}', [AuthController::class, 'deleteUser']);
        Route::post('/admin/programs', [ProgramController::class, 'store']);
        Route::delete('/admin/programs/{id}', [ProgramController::class, 'destroy']);
    });

});
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\PaymentController;

// Redirect root to admin login
Route::get('/', fn() => redirect('/admin/login'));

// Admin routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Auth routes (guest only)
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login']);
    });

    // Protected admin routes
    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/logout', [AuthController::class, 'logout'])->name('logout.get'); // Alternative GET method
        
        // User management
        Route::resource('users', UserController::class);
        
        // Class management
        Route::resource('classes', ClassController::class);
        
        // Payment verification
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::post('/payments/{id}/verify', [PaymentController::class, 'verify'])->name('payments.verify');
        Route::post('/payments/{id}/reject', [PaymentController::class, 'reject'])->name('payments.reject');
    });
});

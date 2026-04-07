<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

// Import Semua Controller
use App\Http\Controllers\WebAuthController;
use App\Http\Controllers\Admin\{
    DashboardController,
    GaleriController,
    PembayaranController,
    PengumumanController,
    JadwalController,
    ManajemenPengajarController,
    ProgramController,
    SiswaAdminController
};
use App\Http\Controllers\Pengajar\{
    PengajarDashboardController,
    MateriController,
    TryoutController
};

/*
|--------------------------------------------------------------------------
| Web Routes - Spekta Academy
|--------------------------------------------------------------------------
*/

// --- 0. AUTHENTICATION ---
Route::get('/', function () { return redirect()->route('login'); });
Route::get('/login', [WebAuthController::class, 'showLogin'])->name('login');
Route::post('/login', [WebAuthController::class, 'login']);
Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');


// --- 1. GROUP ADMINISTRATOR (Role: admin) ---
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard Utama
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // MANAJEMEN SISWA
    Route::prefix('siswa')->name('siswa.')->group(function () {
        Route::get('/semua', [SiswaAdminController::class, 'semuaSiswa'])->name('index');
        Route::get('/daftar-tunggu', [SiswaAdminController::class, 'daftarTunggu'])->name('pendaftaran');
        Route::get('/verifikasi/{id}', [SiswaAdminController::class, 'verifikasi'])->name('verifikasi');
    });

    // ALIAS UNTUK DASHBOARD
    Route::get('/verifikasi-pendaftaran', [SiswaAdminController::class, 'daftarTunggu'])->name('verifikasi.index');

    // MANAJEMEN PENGAJAR
    Route::get('/daftar-pengajar', [ManajemenPengajarController::class, 'index'])->name('pengajar.index');
    Route::resource('manajemen-pengajar', ManajemenPengajarController::class);

    // KEUANGAN / PENDAPATAN
    Route::get('/laporan-pendapatan', [PembayaranController::class, 'index'])->name('pendapatan.index');
    Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
    Route::post('/pembayaran/verifikasi/{id}', [PembayaranController::class, 'verifikasi'])->name('pembayaran.verify');

    // MANAJEMEN PROGRAM KELAS
    Route::prefix('program')->name('program.')->group(function () {
        Route::get('/', [ProgramController::class, 'index'])->name('index');
        Route::post('/store', [ProgramController::class, 'store'])->name('store');
        Route::delete('/delete/{id}', [ProgramController::class, 'destroy'])->name('delete');
    });

    // MANAJEMEN PROMO
    Route::prefix('promo')->name('promo.')->group(function () {
        Route::get('/', [PembayaranController::class, 'promo'])->name('index');
        Route::post('/store', [PembayaranController::class, 'storePromo'])->name('store');
        Route::delete('/destroy/{id}', [PembayaranController::class, 'destroyPromo'])->name('destroy');
        Route::post('/check', [PembayaranController::class, 'checkPromo'])->name('check');
    });

    // Resource Routes
    Route::resource('galeri', GaleriController::class);

    // --- BAGIAN JADWAL (MODIFIKASI DISINI) ---
    // Route untuk mengambil materi via AJAX berdasarkan class_id
    Route::get('/jadwal/get-materials/{class_id}', [JadwalController::class, 'getMaterials'])->name('jadwal.getMaterials');
    Route::resource('jadwal', JadwalController::class);
});


// --- 2. GROUP PENGAJAR (Role: pengajar) ---
Route::middleware(['auth', 'role:pengajar'])->prefix('pengajar')->name('pengajar.')->group(function () {
    Route::get('/dashboard', [PengajarDashboardController::class, 'index'])->name('dashboard');
    Route::get('/jadwal-mengajar', [PengajarDashboardController::class, 'jadwalSaya'])->name('jadwal.index');
    Route::get('/absensi', [PengajarDashboardController::class, 'absensi'])->name('absensi.index');
    Route::get('/absensi/{class_id}', [PengajarDashboardController::class, 'showAbsensi'])->name('absensi.show');
    Route::post('/absensi/simpan', [PengajarDashboardController::class, 'storeAbsensi'])->name('absensi.store');

    Route::resource('materi', MateriController::class);

    Route::get('/soal-tryout', [TryoutController::class, 'buatSoal'])->name('tryout.create');
    Route::get('/nilai', [TryoutController::class, 'lihatNilai'])->name('tryout.nilai');

    // Route materi tambahan (sudah dicover resource di atas sebenarnya, tapi dibiarkan agar tidak mengganggu)
    Route::get('/materi', [MateriController::class, 'index'])->name('materi.index');
    Route::post('/materi', [MateriController::class, 'store'])->name('materi.store');
    Route::delete('/materi/{id}', [MateriController::class, 'destroy'])->name('materi.destroy');
});


// --- 3. HELPER ROUTES ---
Route::get('/view-galeri/{filename}', function ($filename) {
    $path = 'public/galeri/' . $filename;
    if (!Storage::exists($path)) { abort(404); }
    $file = Storage::get($path);
    $type = Storage::mimeType($path);
    return Response::make($file, 200)->header("Content-Type", $type);
});

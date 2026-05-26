<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\ClassModel;
use App\Models\Schedule;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Carbon\Carbon;

class PengajarDashboardController extends Controller
{
    public function index(): View
    {
        return view('pengajar.dashboard');
    }

    public function absensi(): View
    {
        $classes = ClassModel::all();
        $teacherId = Auth::id();
        $today = Carbon::now()->toDateString();

        // Perbaikan: Jika kolom 'date' tidak ada, gunakan 'created_at'
        // atau sesuaikan dengan kolom tanggal di tabel schedules Anda
        $jadwalHariIni = Schedule::where('teacher_id', $teacherId)
                            // ->whereDate('created_at', $today) // Gunakan ini jika kolom 'date' memang tidak ada
                            ->pluck('class_id')
                            ->toArray();

        return view('pengajar.absensi.index', compact('classes', 'jadwalHariIni'));
    }

    public function showAbsensi($class_id): View | \Illuminate\Http\RedirectResponse
    {
        // 1. Cek jadwal (Ganti 'date' dengan kolom yang benar di DB Anda, misal 'created_at')
        $isAssigned = Schedule::where('class_id', $class_id)
                            ->where('teacher_id', Auth::id())
                            // ->whereDate('created_at', Carbon::today())
                            ->first();

        if (!$isAssigned) {
            return redirect()->route('pengajar.dashboard') // Arahkan ke dashboard jika tidak ada akses
                             ->with('info', 'Anda tidak memiliki jadwal di kelas ini hari ini.');
        }

        $class = ClassModel::findOrFail($class_id);

        $siswas = Enrollment::where('class_id', $class_id)
                    ->where('status', 'aktif')
                    ->where('expires_at', '>', now())
                    ->with(['user.student'])
                    ->get();

        return view('pengajar.absensi.show', compact('siswas', 'class', 'isAssigned'));
    }

    public function storeAbsensi(Request $request)
    {
        $request->validate([
            'status' => 'required|array',
            'schedule_id' => 'required'
        ]);

        foreach ($request->status as $userId => $status) {
            Attendance::create([
                'schedule_id' => $request->schedule_id,
                'user_id'     => $userId,
                'status'      => $status,
                'date'        => now()->toDateString()
            ]);
        }

        return redirect()->route('pengajar.dashboard')->with('success', 'Absensi berhasil disimpan!');
    }

    public function jadwalSaya(): View
    {
        $jadwal = Schedule::where('teacher_id', Auth::id())
                    ->with('classModel')
                    ->orderBy('id', 'desc') // Ganti 'date' dengan 'id' atau 'created_at' agar tidak error
                    ->get();

        return view('pengajar.jadwal.index', compact('jadwal'));
    }
}

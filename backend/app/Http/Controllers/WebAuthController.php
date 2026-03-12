<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Otp;
use App\Models\Student;
use App\Mail\SendOTPMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WebAuthController extends Controller
{
    // --- FITUR WEB (ADMIN & PENGAJAR) ---

    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectUser(Auth::user());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return $this->redirectUser(Auth::user());
        }

        return back()->with('error', 'Gmail atau Password salah!');
    }

    private function redirectUser($user)
    {
        if ($user->role_id == 1) return redirect()->intended('/admin/dashboard');
        if ($user->role_id == 2) return redirect()->intended('/pengajar/dashboard');

        // Jika Siswa mencoba login di Web
        Auth::logout();
        return redirect('/login')->with('error', 'Siswa hanya dapat login melalui Aplikasi Mobile.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }


    // --- FITUR API MOBILE (REGISTRASI & OTP SISWA) ---

    /**
     * Registrasi Siswa Baru & Kirim OTP ke Email
     */
    public function register(Request $request)
    {
        // 1. Validasi Input dari Flutter
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'nomor_wa' => 'required',
            'password' => 'required|min:8|confirmed', // 'confirmed' butuh field password_confirmation
        ]);

        try {
            return DB::transaction(function () use ($request) {
                // 2. Buat User (Status Inactive/Belum Terverifikasi)
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->nomor_wa,
                    'role_id' => 3, // Role Siswa
                    'password' => Hash::make($request->password),
                ]);

                // 3. Generate 6 Digit OTP
                $otpCode = random_int(100000, 999999);

                // 4. Simpan OTP ke Database
                Otp::create([
                    'email' => $request->email,
                    'otp_code' => $otpCode,
                    'expired_at' => Carbon::now()->addMinutes(5), // Berlaku 5 menit
                ]);

                // 5. Kirim Email OTP ke Gmail Siswa
                Mail::to($request->email)->send(new SendOTPMail($otpCode));

                return response()->json([
                    'status' => 'success',
                    'message' => 'Registrasi berhasil. Silakan cek email Anda untuk kode OTP.',
                    'otp' => $otpCode // Hapus ini saat sudah produksi (hanya untuk testing)
                ], 201);
            });
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verifikasi Kode OTP dari Flutter
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp_code' => 'required|numeric',
        ]);

        // Cek apakah OTP valid, milik email tersebut, belum dipakai, dan belum expired
        $otpData = Otp::where('email', $request->email)
                    ->where('otp_code', $request->otp_code)
                    ->where('is_used', false)
                    ->where('expired_at', '>', Carbon::now())
                    ->latest()
                    ->first();

        if (!$otpData) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kode OTP salah atau sudah kadaluarsa.'
            ], 422);
        }

        // Jika Benar:
        $otpData->update(['is_used' => true]);

        // Berikan status verifikasi pada User (Opsional: update email_verified_at)
        User::where('email', $request->email)->update([
            'email_verified_at' => Carbon::now()
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Email berhasil diverifikasi. Selamat bergabung di Spekta Academy!'
        ], 200);
    }
}
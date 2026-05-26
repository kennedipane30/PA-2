<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\OtpCode; // Import Model OTP
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'whatsapp' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            DB::beginTransaction(); // Gunakan transaksi agar jika satu gagal, semua batal

            // 2. Buat User Baru
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'whatsapp' => $request->whatsapp,
                'password' => Hash::make($request->password),
                'role_id'  => 3, // Role Siswa
            ]);

            // 3. Buat Detail Student (Sesuai kolom yang kita modif tadi)
            Student::create([
                'user_id' => $user->user_id,
                'school'  => $request->school ?? '-',
                'grade'   => $request->grade ?? '-',
            ]);

            // 4. GENERATE OTP
            $otp = rand(1000, 9999);
            
            // 5. SIMPAN KE TABEL OTP_CODES
            OtpCode::create([
                'user_id'    => $user->user_id,
                'email'      => $user->email,
                'otp_code'   => $otp,
                'expired_at' => Carbon::now()->addMinutes(10),
                'is_used'    => false
            ]);

            DB::commit();

            // 6. KIRIM OTP (Contoh lewat Log dulu untuk testing)
            // Kamu bisa ganti dengan Mail::send atau WhatsApp API
            \Log::info("OTP untuk {$user->email} adalah: {$otp}");

            return response()->json([
                'success' => true,
                'message' => 'Registrasi berhasil, silakan cek OTP.',
                'user_id' => $user->user_id
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal registrasi: ' . $e->getMessage()
            ], 500);
        }
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Profile;
use App\Models\OtpCode; // Pastikan model ini sudah dibuat
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AuthController extends Controller
{
    // --- FITUR REGISTRASI SISWA ---
    public function registerSiswa(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'tanggal_lahir' => 'required|date',
            'nomor_wa' => 'required|string',
            'nomor_wa_ortu' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => 3, // Role Siswa
            ]);

            Profile::create([
                'user_id' => $user->id,
                'tanggal_lahir' => $request->tanggal_lahir,
                'nomor_wa' => $request->nomor_wa,
                'nomor_wa_ortu' => $request->nomor_wa_ortu,
            ]);

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Registrasi Berhasil'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // --- FITUR LOGIN (Step 1: Kirim OTP) ---
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::where('email', $request->email)->first();

        // Cek User & Password
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['status' => 'error', 'message' => 'Email atau Password salah'], 401);
        }

        // Buat 6 Digit OTP Acak
        $otp = rand(100000, 999999);

        // Simpan/Update OTP di Database
        OtpCode::updateOrCreate(
            ['user_id' => $user->id],
            [
                'otp' => $otp,
                'valid_until' => Carbon::now()->addMinutes(5) // Berlaku 5 menit
            ]
        );

        // TODO: Integrasi WhatsApp Gateway di sini
        // Sementara kita return di JSON agar kamu bisa tes
        return response()->json([
            'status' => 'success',
            'message' => 'Kode OTP berhasil dikirim (Simulasi)',
            'otp' => $otp // Hapus ini jika sudah pakai WA Gateway beneran
        ]);
    }

    // --- FITUR VERIFIKASI OTP (Step 2: Dapatkan Token) ---
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::where('email', $request->email)->first();

        // Cari OTP yang cocok dan belum expired
        $otpRecord = OtpCode::where('user_id', $user->id)
                            ->where('otp', $request->otp)
                            ->where('valid_until', '>', Carbon::now())
                            ->first();

        if (!$otpRecord) {
            return response()->json(['status' => 'error', 'message' => 'Kode OTP salah atau kadaluarsa'], 401);
        }

        // Hapus OTP setelah sukses digunakan (Keamanan)
        $otpRecord->delete();

        // Buat Token Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login Berhasil',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user->load('role') // Memuat info role
        ]);
    }

    // --- FITUR LOGOUT ---
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Berhasil Logout']);
    }
}

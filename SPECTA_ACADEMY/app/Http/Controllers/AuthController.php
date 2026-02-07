<?php

namespace App\Http\Controllers;

use App\Models\{User, Profile, OtpCode};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Hash, Validator, DB, Http};
use Carbon\Carbon;

class AuthController extends Controller {
    public function registerSiswa(Request $request)
{
    // Validasi Keamanan Tingkat Tinggi (Matkul: Keamanan Perangkat Lunak)
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => [
            'required',
            'confirmed',
            'min:8',             // Minimal 8 karakter
            'regex:/[a-z]/',      // Harus ada huruf kecil
            'regex:/[A-Z]/',      // Harus ada huruf BESAR (Kapital)
            'regex:/[0-9]/',      // Harus ada ANGKA
            'regex:/[@$!%*#?&]/', // Harus ada SIMBOL
        ],
        'tanggal_lahir' => 'required|date',
        'alamat' => 'required|string',
        'nomor_wa' => 'required|string',
        'nomor_wa_ortu' => 'required|string',
    ], [
        'password.regex' => 'Password harus mengandung Huruf Kapital, Angka, dan Simbol!'
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
            'role_id' => 3, // Siswa
        ]);

        Profile::create([
            'user_id' => $user->id,
            'tanggal_lahir' => $request->tanggal_lahir,
            'alamat' => $request->alamat,
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
    public function login(Request $request) {
        $user = User::where('email', $request->email)->with('profile')->first();
        if (!$user || !Hash::check($request->password, $user->password)) return response()->json(['message' => 'Akun tidak ditemukan'], 401);

        $otp = rand(100000, 999999);
        OtpCode::updateOrCreate(['user_id' => $user->id], ['otp' => $otp, 'valid_until' => Carbon::now()->addMinutes(5)]);

        $nomor_wa = str_starts_with($user->profile->nomor_wa, '0') ? '62' . substr($user->profile->nomor_wa, 1) : $user->profile->nomor_wa;

        Http::withHeaders(['Authorization' => env('FONNTE_TOKEN')])->post('https://api.fonnte.com/send', [
            'target' => $nomor_wa,
            'message' => "KODE OTP SPEKTA: *$otp*. Rahasiakan kode ini.",
        ]);

        return response()->json(['status' => 'success', 'message' => 'OTP terkirim', 'otp_test' => $otp]);
    }

    public function verifyOtp(Request $request) {
        $user = User::where('email', $request->email)->first();
        $otpRecord = OtpCode::where('user_id', $user->id)->where('otp', $request->otp)->where('valid_until', '>', Carbon::now())->first();
        if (!$otpRecord) return response()->json(['message' => 'OTP Salah/Expired'], 401);
        $otpRecord->delete();
        return response()->json(['token' => $user->createToken('token')->plainTextToken, 'user' => $user]);
    }
}

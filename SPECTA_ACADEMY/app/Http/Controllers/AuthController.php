<?php
namespace App\Http\Controllers;

use App\Models\{User, Student, OtpCode, Enrollment};
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Support\Facades\{Hash, Validator, DB, Auth};
use Carbon\Carbon;

class AuthController extends Controller {

    // --- FUNGSI REGISTRASI, LOGIN, VERIFIKASI (TETAP SAMA) ---
    public function registerSiswa(Request $request): JsonResponse {
        $v = Validator::make($request->all(), [
            'name' => 'required|regex:/^[a-zA-Z\s]+$/|unique:users',
            'email' => 'required|email|unique:users',
            'nomor_wa' => 'required',
            'password' => ['required', 'confirmed', 'min:8', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[@$!%*#?&]/'],
        ]);
        if ($v->fails()) return response()->json(['status' => 'error', 'message' => $v->errors()->first()], 422);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name, 'email' => $request->email, 'phone' => $request->nomor_wa,
                'password' => bcrypt($request->password), 'role_id' => 3, 'is_verified' => false
            ]);
            Student::create(['user_id' => $user->usersID, 'school' => '-', 'grade' => '12 IPA', 'dob' => now(), 'wa_ortu' => '-']);
            $otp = rand(100000, 999999);
            OtpCode::updateOrCreate(['user_id' => $user->usersID], ['otp' => $otp, 'valid_until' => Carbon::now()->addMinutes(10)]);
            DB::commit();
            return response()->json(['status' => 'success', 'otp' => $otp, 'name' => $user->name], 201);
        } catch (\Exception $e) { DB::rollBack(); return response()->json(['message' => $e->getMessage()], 500); }
    }

public function verifyRegistration(Request $request): JsonResponse
{
    // 1. Ambil data dari request
    $namaSiswa = $request->name;
    $kodeOtp = $request->otp;

    // 2. Cari user berdasarkan Nama (Pastikan bukan Null)
    $user = User::where('name', $namaSiswa)->first();

    if (!$user) {
        return response()->json(['status' => 'error', 'message' => 'User tidak ditemukan'], 404);
    }

    // 3. Cari OTP yang cocok untuk user tersebut di tabel otp_codes
    // Pastikan kolom relasi adalah user_id dan merujuk ke usersID
    $otpRecord = OtpCode::where('user_id', $user->usersID)
                        ->where('otp', $kodeOtp)
                        ->where('valid_until', '>', now())
                        ->first();

    if (!$otpRecord) {
        return response()->json(['status' => 'error', 'message' => 'Kode OTP Salah atau Kadaluarsa'], 401);
    }

    // 4. PROSES UPDATE STATUS (Paling Penting!)
    // Kita paksa update langsung ke database
    DB::table('users')->where('usersID', $user->usersID)->update(['is_verified' => true]);

    // 5. Hapus OTP agar tidak bisa dipakai lagi
    $otpRecord->delete();

    return response()->json([
        'status' => 'success',
        'message' => 'Akun berhasil diaktifkan!'
    ], 200);
}

    public function login(Request $request): JsonResponse {
        $user = User::where('name', $request->name)->first();
        if (!$user || !Hash::check($request->password, $user->password)) return response()->json(['status' => 'error', 'message' => 'Nama/Password Salah'], 401);
        if (!$user->is_verified) return response()->json(['status' => 'error', 'message' => 'Akun belum verifikasi WA!'], 403);
        return response()->json(['status' => 'success', 'token' => $user->createToken('T')->plainTextToken, 'user' => $user->load('student')]);
    }

    // --- FUNGSI UPDATE PROFIL (SUDAH DIRAPIKAN) ---

    // app/Http/Controllers/AuthController.php

public function updateProfile(Request $request): JsonResponse {
    $v = Validator::make($request->all(), [
        'parent_name' => 'required|string|max:255',
        'alamat' => 'required|string',
        'wa_ortu' => 'required|numeric',
    ]);

    if ($v->fails()) return response()->json(['status' => 'error', 'message' => $v->errors()->first()], 422);

    /** @var \App\Models\User $user */
    $user = Auth::user();

    $user->student->update([
        'parent_name' => $request->parent_name,
        'school' => $request->alamat, // Alamat kita simpan di kolom school sesuai ERD
        'wa_ortu' => $request->wa_ortu,
    ]);

    return response()->json(['status' => 'success', 'message' => 'Data diri anda berhasil dilengkapi'], 200);
}

public function joinClass(Request $request): JsonResponse {
    /** @var \App\Models\User $user */
    $user = Auth::user();

    // LOGIKA PENJAGA GERBANG: Cek kelengkapan data
    $s = $user->student;
    if (empty($s->parent_name) || $s->school == "-" || empty($s->wa_ortu)) {
        return response()->json([
            'status' => 'error',
            'message' => 'Data profil belum lengkap! Silakan lengkapi di menu Akun.'
        ], 403); // 403 Forbidden
    }

    Enrollment::create([
        'user_id' => $user->usersID,
        'class_id' => $request->class_id,
        'status' => 'pending'
    ]);

    return response()->json(['status' => 'success', 'message' => 'Menunggu verifikasi admin']);
}
}


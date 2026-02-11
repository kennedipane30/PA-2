<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Profile;
use App\Models\OtpCode;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse; // Tambahkan ini agar JsonResponse tidak merah

class AuthController extends Controller
{
    /**
     * FUNGSI REGISTRASI SISWA
     */
    public function registerSiswa(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => [
                'required', 'confirmed', 'min:8',
                'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[@$!%*#?&]/'
            ],
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string',
            'nomor_wa' => 'required|string',
            'nomor_wa_ortu' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);
        }

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => 3,
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

    /**
     * FUNGSI LOGIN
     */
    public function login(Request $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['status' => 'error', 'message' => 'Gmail atau Password salah'], 401);
        }

        $otp = rand(100000, 999999);
        OtpCode::updateOrCreate(
            ['user_id' => $user->id],
            ['otp' => $otp, 'valid_until' => Carbon::now()->addMinutes(5)]
        );

        return response()->json([
            'status' => 'success',
            'otp' => $otp,
            'email' => $user->email
        ]);
    }

    /**
     * FUNGSI VERIFIKASI OTP
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();
        if (!$user) return response()->json(['status' => 'error', 'message' => 'User tidak ditemukan'], 404);

        $otpRecord = OtpCode::where('user_id', $user->id)
                            ->where('otp', $request->otp)
                            ->where('valid_until', '>', Carbon::now())
                            ->first();

        if (!$otpRecord) {
            return response()->json(['status' => 'error', 'message' => 'OTP Salah/Expired'], 401);
        }

        $otpRecord->delete();

        return response()->json([
            'status' => 'success',
            'token' => $user->createToken('token')->plainTextToken,
            'user' => $user->load('profile', 'role')
        ]);
    }

    /**
     * FUNGSI CEK STATUS KELAS (SUDAH DIPERBAIKI)
     */
    public function checkClassStatus(Request $request): JsonResponse
    {
        // Variabel disamakan menjadi $enroll agar tidak error
        $enroll = Enrollment::where('user_id', Auth::id())
                            ->where('class_id', $request->class_id)
                            ->first();

        return response()->json([
            'status' => $enroll ? $enroll->status : 'none'
        ]);
    }

    /**
     * FUNGSI GABUNG KELAS
     */
    public function joinClass(Request $request): JsonResponse
    {
        Enrollment::create([
            'user_id' => Auth::id(),
            'class_id' => $request->class_id,
            'status' => 'pending'
        ]);

        return response()->json(['status' => 'success', 'message' => 'Menunggu verifikasi admin']);
    }

    /**
     * FUNGSI LOGOUT
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['status' => 'success', 'message' => 'Berhasil Logout']);
    }
}

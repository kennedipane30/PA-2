<?php

namespace App\Http\Controllers;

use App\Models\{User, Student, OtpCode, Enrollment};
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Support\Facades\{Hash, Validator, DB, Auth};
use Carbon\Carbon;

class AuthController extends Controller {

    public function registerSiswa(Request $request): JsonResponse {
        $v = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => ['required', 'confirmed', 'min:8', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[@$!%*#?&]/'],
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string',
            'nomor_wa' => 'required|string',
            'nomor_wa_ortu' => 'required|string',
        ]);

        if ($v->fails()) return response()->json(['status' => 'error', 'message' => $v->errors()->first()], 422);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->nomor_wa,
                'password' => bcrypt($request->password), // Bcrypt Manual
                'role_id' => 3, // Role Siswa
            ]);

            Student::create([
                'user_id' => $user->usersID,
                'school' => $request->alamat,
                'grade' => '12 IPA',
                'dob' => $request->tanggal_lahir,
                'wa_ortu' => $request->nomor_wa_ortu,
            ]);

            DB::commit();
            return response()->json(['status' => 'success'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function login(Request $request): JsonResponse {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['status' => 'error', 'message' => 'Gmail atau Password Salah!'], 401);
        }

        // Proteksi: Hanya Siswa (Role 3) yang boleh login di Mobile
        if ($user->role_id != 3) {
            return response()->json(['status' => 'error', 'message' => 'Hanya akun Siswa yang bisa login di sini!'], 403);
        }

        $otp = rand(100000, 999999);
        OtpCode::updateOrCreate(
            ['user_id' => $user->usersID],
            ['otp' => $otp, 'valid_until' => Carbon::now()->addMinutes(5)]
        );

        return response()->json(['status' => 'success', 'otp' => $otp, 'email' => $user->email]);
    }

    public function verifyOtp(Request $request): JsonResponse {
        $user = User::where('email', $request->email)->first();
        if (!$user) return response()->json(['status' => 'error', 'message' => 'User tidak ditemukan'], 404);

        $otpRecord = OtpCode::where('user_id', $user->usersID)
                            ->where('otp', $request->otp)
                            ->where('valid_until', '>', now())
                            ->first();

        if (!$otpRecord) return response()->json(['status' => 'error', 'message' => 'Kode OTP Salah/Kadaluarsa'], 401);

        $otpRecord->delete();

        return response()->json([
            'status' => 'success',
            'token' => $user->createToken('token')->plainTextToken,
            'user' => $user->load('student')
        ]);
    }

    public function checkClassStatus(Request $request): JsonResponse {
        $enroll = Enrollment::where('user_id', Auth::id())->where('class_id', $request->class_id)->first();
        return response()->json(['status' => $enroll ? $enroll->status : 'none']);
    }

    public function joinClass(Request $request): JsonResponse {
        Enrollment::create(['user_id' => Auth::id(), 'class_id' => $request->class_id, 'status' => 'pending']);
        return response()->json(['status' => 'success', 'message' => 'Menunggu verifikasi admin']);
    }
}

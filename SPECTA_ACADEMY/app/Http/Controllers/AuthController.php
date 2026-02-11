<?php

namespace App\Http\Controllers;

use App\Models\{User, Student, OtpCode, Enrollment};
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Support\Facades\{Hash, Validator, DB, Auth};
use Carbon\Carbon;

class AuthController extends Controller {

    public function registerSiswa(Request $request): JsonResponse {
        $v = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => ['required', 'confirmed', 'min:8', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[@$!%*#?&]/'],
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required',
            'nomor_wa' => 'required',
            'nomor_wa_ortu' => 'required'
        ]);

        if ($v->fails()) return response()->json(['status' => 'error', 'message' => $v->errors()->first()], 422);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->nomor_wa,
                'password' => $request->password, // MODIFIKASI: Jangan pakai Hash::make (Double Hashing)
                'role_id' => 3
            ]);

            Student::create([
                'user_id' => $user->usersID,
                'school' => $request->alamat,
                'grade' => '12 IPA',
                'dob' => $request->tanggal_lahir,
                'wa_ortu' => $request->nomor_wa_ortu
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

        // Debugging sederhana: cek email dulu
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Gmail tidak terdaftar'], 401);
        }

        // Cek password
        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['status' => 'error', 'message' => 'Password salah'], 401);
        }

        $otp = rand(100000, 999999);
        OtpCode::updateOrCreate(
            ['user_id' => $user->usersID],
            ['otp' => $otp, 'valid_until' => Carbon::now()->addMinutes(5)]
        );

        return response()->json([
            'status' => 'success',
            'otp' => $otp,
            'email' => $user->email
        ]);
    }

    public function verifyOtp(Request $request): JsonResponse {
        $user = User::where('email', $request->email)->first();
        if (!$user) return response()->json(['status' => 'error', 'message' => 'User hilang'], 404);

        $otp = OtpCode::where('user_id', $user->usersID)
                      ->where('otp', $request->otp)
                      ->where('valid_until', '>', now())
                      ->first();

        if (!$otp) return response()->json(['status' => 'error', 'message' => 'OTP Salah/Kadaluarsa'], 401);

        $otp->delete();
        return response()->json([
            'status' => 'success',
            'token' => $user->createToken('token')->plainTextToken,
            'user' => $user->load('student')
        ]);
    }

    // Fungsi checkClassStatus & joinClass tetap sama...
    public function checkClassStatus(Request $request): JsonResponse {
        $enroll = Enrollment::where('user_id', Auth::id())->where('class_id', $request->class_id)->first();
        return response()->json(['status' => $enroll ? $enroll->status : 'none']);
    }

    public function joinClass(Request $request): JsonResponse {
        Enrollment::create(['user_id' => Auth::id(), 'class_id' => $request->class_id, 'status' => 'pending']);
        return response()->json(['status' => 'success', 'message' => 'Menunggu Admin']);
    }
}

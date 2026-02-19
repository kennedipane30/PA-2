<?php
namespace App\Http\Controllers;

use App\Models\{User, Student, OtpCode, Enrollment};
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Support\Facades\{Hash, Validator, DB, Auth};
use Carbon\Carbon;

class AuthController extends Controller {

    public function registerSiswa(Request $request): JsonResponse {
        $v = Validator::make($request->all(), [
            'name' => 'required|regex:/^[a-zA-Z\s]+$/|unique:users', // Hanya huruf & spasi
            'email' => 'required|email|unique:users',
            'nomor_wa' => 'required',
            'password' => ['required', 'confirmed', 'min:8', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[@$!%*#?&]/'],
        ], [
            'name.regex' => 'Nama hanya boleh berisi huruf!',
            'password.regex' => 'Password wajib: Huruf Kapital, Huruf Biasa, Angka, dan Simbol!'
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

    public function verifyRegistration(Request $request): JsonResponse {
        $user = User::where('name', $request->name)->first();
        if (!$user) return response()->json(['message' => 'User tidak ada'], 404);

        $otp = OtpCode::where('user_id', $user->usersID)->where('otp', $request->otp)->where('valid_until', '>', now())->first();
        if (!$otp) return response()->json(['status' => 'error', 'message' => 'OTP Salah/Expired'], 401);

        $user->update(['is_verified' => true]);
        $otp->delete();
        return response()->json(['status' => 'success']);
    }

    public function login(Request $request): JsonResponse {
        $user = User::where('name', $request->name)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['status' => 'error', 'message' => 'Nama atau Password Salah'], 401);
        }
        if (!$user->is_verified) {
            return response()->json(['status' => 'error', 'message' => 'Akun belum verifikasi WA!'], 403);
        }
        return response()->json(['status' => 'success', 'token' => $user->createToken('T')->plainTextToken, 'user' => $user->load('student')]);
    }

    public function checkClassStatus(Request $request): JsonResponse {
        $enroll = Enrollment::where('user_id', Auth::id())->where('class_id', $request->class_id)->first();
        return response()->json(['status' => $enroll ? $enroll->status : 'none']);
    }
}

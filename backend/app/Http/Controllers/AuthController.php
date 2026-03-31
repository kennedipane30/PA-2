<?php

namespace App\Http\Controllers;

use App\Models\{User, Student, OtpCode, Enrollment, Material, Schedule, Tryout, Question, TryoutResult};
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Support\Facades\{Hash, Validator, DB, Auth, Log}; 
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use SendGrid\Mail\Mail;

class AuthController extends Controller {

    /**
     * FUNCTION KIRIM OTP EMAIL (SENDGRID)
     */
    private function sendOtpEmail($email, $name, $otp)
    {
        $emailSender = new Mail();
        $emailSender->setFrom(env('SENDGRID_FROM_EMAIL'), env('SENDGRID_FROM_NAME'));
        $emailSender->setSubject("Kode OTP Specta Academy");
        $emailSender->addTo($email, $name);
        $emailSender->addContent(
            "text/html",
            "<div style='font-family:Arial;padding:20px;border:1px solid #eee;'>
                <h2 style='color:#990000;'>Verifikasi Akun Specta Academy</h2>
                <p>Halo <b>$name</b>, kode OTP Anda adalah:</p>
                <h1 style='letter-spacing:5px;background:#f4f4f4;padding:10px;text-align:center;'>$otp</h1>
                <p>Berlaku selama 10 menit. Jangan berikan kode ini kepada siapapun.</p>
            </div>"
        );

        $sendgrid = new \SendGrid(env('SENDGRID_API_KEY'));
        try {
            $sendgrid->send($emailSender);
        } catch (\Exception $e) {
            Log::error("SendGrid Error: " . $e->getMessage());
        }
    }

    /**
     * 1️⃣ REGISTRASI SISWA
     */
    public function registerSiswa(Request $request): JsonResponse {
        $v = Validator::make($request->all(), [
            'name' => 'required|regex:/^[a-zA-Z\s]+$/|unique:users',
            'email' => 'required|email|unique:users',
            'nomor_wa' => 'required',
            'password' => 'required|confirmed|min:8',
        ]);

        if ($v->fails()) {
            return response()->json(['status' => 'error', 'message' => $v->errors()->first()], 422);
        }

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => trim($request->name),
                'email' => strtolower(trim($request->email)),
                'phone' => $request->nomor_wa,
                'password' => bcrypt($request->password),
                'role_id' => 3, 
                'is_verified' => false
            ]);

            Student::create([
                'user_id' => $user->user_id,
                'school' => '-',
                'grade' => '12 IPA'
            ]);

            $otp = rand(100000, 999999);
            OtpCode::updateOrCreate(
                ['user_id' => $user->user_id],
                ['email' => $user->email, 'otp_code' => (string)$otp, 'expired_at' => Carbon::now()->addMinutes(10)]
            );

            $this->sendOtpEmail($user->email, $user->name, $otp);
            DB::commit();

            return response()->json(['status' => 'success', 'message' => 'OTP dikirim ke email'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * 2️⃣ VERIFIKASI OTP (FIXED UNTUK PostgreSQL & usersID)
     */
    public function verifyRegistration(Request $request): JsonResponse {
        $request->validate(['email' => 'required|email', 'otp' => 'required']);
        
        $email = strtolower(trim($request->email));
        $user = User::where('email', $email)->first();

        if (!$user) return response()->json(['status' => 'error', 'message' => 'User tidak ditemukan'], 404);

        $otpRecord = OtpCode::where('user_id', $user->user_id)->where('otp_code', $request->otp)->first();

        if (!$otpRecord || Carbon::parse($otpRecord->expired_at)->isPast()) {
            return response()->json(['status' => 'error', 'message' => 'OTP salah atau kadaluarsa'], 401);
        }

        // Gunakan DB::table untuk menghindari masalah Primary Key di Eloquent saat update
        DB::table('users')->where('user_id', $user->user_id)->update([
            'is_verified' => true,
            'updated_at' => now()
        ]);

        $otpRecord->delete();
        return response()->json(['status' => 'success', 'message' => 'Akun berhasil aktif!']);
    }

    /**
     * 3️⃣ LOGIN (MENGGUNAKAN USERNAME/NAMA)
     */
    public function login(Request $request): JsonResponse {
        $request->validate([
            'username' => 'required|string', 
            'password' => 'required|string'
        ]);

        // Cari di kolom 'name' (username) menggunakan ILIKE agar tidak case sensitive
        $user = User::where('name', 'ILIKE', trim($request->username))->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['status' => 'error', 'message' => 'Username atau Password salah'], 401);
        }

        if (!$user->is_verified) {
            return response()->json(['status' => 'error', 'message' => 'Akun belum diverifikasi'], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'status' => 'success',
            'token' => $token,
            'user' => $user->load('student', 'role')
        ]);
    }

    /**
     * 4️⃣ LUPA PASSWORD
     */
    public function forgotPassword(Request $request): JsonResponse {
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', strtolower(trim($request->email)))->first();

        if (!$user) return response()->json(['status' => 'error', 'message' => 'Email tidak terdaftar'], 404);

        $otp = rand(100000, 999999);
        OtpCode::updateOrCreate(
            ['user_id' => $user->user_id],
            ['email' => $user->email, 'otp_code' => (string)$otp, 'expired_at' => Carbon::now()->addMinutes(10)]
        );

        $this->sendOtpEmail($user->email, $user->name, $otp);
        return response()->json(['status' => 'success', 'message' => 'OTP reset dikirim ke email']);
    }

    /**
     * 5️⃣ RESET PASSWORD
     */
    public function resetPassword(Request $request): JsonResponse {
        $request->validate(['email' => 'required|email', 'otp' => 'required', 'password' => 'required|confirmed|min:8']);
        
        $user = User::where('email', strtolower(trim($request->email)))->first();
        if (!$user) return response()->json(['status' => 'error', 'message' => 'User tidak ditemukan'], 404);

        $otpRecord = OtpCode::where('user_id', $user->user_id)->where('otp_code', $request->otp)->first();
        if (!$otpRecord || Carbon::parse($otpRecord->expired_at)->isPast()) {
            return response()->json(['status' => 'error', 'message' => 'OTP salah atau kadaluarsa'], 401);
        }

        $user->update(['password' => bcrypt($request->password)]);
        $otpRecord->delete();
        return response()->json(['status' => 'success', 'message' => 'Password berhasil direset']);
    }

    /**
     * 6️⃣ LOGOUT
     */
    public function logout(Request $request): JsonResponse {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['status' => 'success', 'message' => 'Berhasil logout']);
    }

    /**
     * 7️⃣ ADMIN: GET ALL USERS
     */
    public function getAllUsers(): JsonResponse {
        $users = User::with('role')->where('user_id', '!=', Auth::id())->orderBy('created_at', 'desc')->get();
        return response()->json(['status' => 'success', 'data' => $users]);
    }

    /**
     * 8️⃣ ADMIN: DELETE USER
     */
    public function deleteUser($user_id): JsonResponse {
        $user = User::find($user_id);
        if (!$user || $user->role_id == 1) return response()->json(['status' => 'error', 'message' => 'Gagal menghapus'], 403);
        $user->delete();
        return response()->json(['status' => 'success', 'message' => 'User berhasil dihapus']);
    }
}
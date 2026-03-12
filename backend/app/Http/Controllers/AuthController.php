<?php

namespace App\Http\Controllers;

use App\Models\{User, Student, OtpCode, Enrollment, Material, Schedule, Tryout, Question, TryoutResult};
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Support\Facades\{Hash, Validator, DB, Auth, Log}; // Tambahkan Log
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
            "
            <h2>Verifikasi Akun Specta Academy</h2>
            <p>Halo <b>$name</b>,</p>
            <p>Kode OTP kamu adalah:</p>
            <h1>$otp</h1>
            <p>Kode berlaku selama 10 menit.</p>
            <br>
            <small>Jangan bagikan kode ini kepada siapapun.</small>
            "
        );

        $sendgrid = new \SendGrid(env('SENDGRID_API_KEY'));

        try {
            $sendgrid->send($emailSender);
        } catch (\Exception $e) {
            Log::error("SendGrid Error: " . $e->getMessage());
            throw new \Exception("Gagal mengirim email OTP");
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
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/'
            ],
        ]);

        if ($v->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $v->errors()->first()
            ], 422);
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
                'user_id' => $user->usersID,
                'school' => '-',
                'grade' => '12 IPA',
                'dob' => null,
                'wa_ortu' => '-',
                'parent_name' => '-'
            ]);

            // Generate OTP
            $otp = rand(100000, 999999);

            OtpCode::updateOrCreate(
                ['user_id' => $user->usersID],
                [
                    'email'      => $user->email,
                    'otp_code'   => (string)$otp, // Pastikan string
                    'expired_at' => Carbon::now()->addMinutes(10)
                ]
            );

            $this->sendOtpEmail($user->email, $user->name, $otp);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'OTP berhasil dikirim ke email'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Register Error: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 2️⃣ VERIFIKASI OTP REGISTRASI
     */
    public function verifyRegistration(Request $request): JsonResponse {

        // Logging untuk Debugging
        Log::info("Request Verifikasi Masuk: ", $request->all());

        $request->validate([
            'email' => 'required|email',
            'otp' => 'required'
        ]);

        $email = strtolower(trim($request->email));
        $otpCodeInput = trim($request->otp);

        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email ' . $email . ' tidak ditemukan di sistem'
            ], 404);
        }

        // Cari OTP berdasarkan user_id dan kode OTP-nya
        $otpRecord = OtpCode::where('user_id', $user->usersID)
            ->where('otp_code', $otpCodeInput)
            ->first();

        if (!$otpRecord) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kode OTP salah'
            ], 401);
        }

        // Cek apakah expired
        if (Carbon::parse($otpRecord->expired_at)->isPast()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kode OTP sudah kadaluarsa'
            ], 401);
        }

        $user->update([
            'is_verified' => true
        ]);

        $otpRecord->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Akun berhasil diverifikasi'
        ]);
    }

    /**
     * 3️⃣ LOGIN
     */
    public function login(Request $request): JsonResponse {

        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', strtolower(trim($request->email)))->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email atau Password salah'
            ], 401);
        }

        if (!$user->is_verified) {
            return response()->json([
                'status' => 'error',
                'message' => 'Akun belum diverifikasi'
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'token' => $token,
            'user' => $user->load('student')
        ]);
    }

    /**
     * 4️⃣ LUPA PASSWORD (KIRIM OTP)
     */
    public function forgotPassword(Request $request): JsonResponse {

        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', strtolower(trim($request->email)))->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email tidak terdaftar'
            ], 404);
        }

        $otp = rand(100000, 999999);

        OtpCode::updateOrCreate(
            ['user_id' => $user->usersID],
            [
                'email'      => $user->email,
                'otp_code'   => (string)$otp,
                'expired_at' => Carbon::now()->addMinutes(10)
            ]
        );

        $this->sendOtpEmail($user->email, $user->name, $otp);

        return response()->json([
            'status' => 'success',
            'message' => 'OTP reset password dikirim ke email'
        ]);
    }

    /**
     * 5️⃣ RESET PASSWORD
     */
    public function resetPassword(Request $request): JsonResponse {

        $v = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required',
            'password' => [
                'required',
                'confirmed',
                'min:8'
            ]
        ]);

        if ($v->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $v->errors()->first()
            ], 422);
        }

        $user = User::where('email', strtolower(trim($request->email)))->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        $otpRecord = OtpCode::where('user_id', $user->usersID)
            ->where('otp_code', trim($request->otp))
            ->first();

        if (!$otpRecord) {
            return response()->json([
                'status' => 'error',
                'message' => 'OTP salah'
            ], 401);
        }

        if (Carbon::parse($otpRecord->expired_at)->isPast()) {
            return response()->json([
                'status' => 'error',
                'message' => 'OTP kadaluarsa'
            ], 401);
        }

        $user->update([
            'password' => bcrypt($request->password)
        ]);

        $otpRecord->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Password berhasil direset'
        ]);
    }

    /**
     * 6️⃣ LOGOUT
     */
    public function logout(Request $request): JsonResponse {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil logout'
        ]);
    }
}
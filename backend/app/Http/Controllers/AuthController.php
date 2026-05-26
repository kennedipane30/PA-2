<?php

namespace App\Http\Controllers;

use App\Models\{User, Student, OtpCode, Enrollment, Material, Schedule, Tryout, Question, TryoutResult};
use App\Mail\SendOTPMail;
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Support\Facades\{Hash, Validator, DB, Auth, Log, Mail}; 
use Carbon\Carbon;

class AuthController extends Controller {

    /**
     * FUNCTION KIRIM OTP EMAIL (SENDGRID)
     */
    private function sendOtpEmail($email, $name, $otp)
    {
        try {
            Mail::to($email)->send(new SendOTPMail($otp));
        } catch (\Exception $e) {
            Log::error("Gagal mengirim OTP ke {$email}: " . $e->getMessage());
            throw $e;
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

            // Gunakan NULL agar pengecekan lebih akurat
            Student::create([
                'user_id' => $user->user_id,
                'school' => null, 
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
     * 2️⃣ VERIFIKASI OTP
     */
    public function verifyRegistration(Request $request): JsonResponse {
        $request->validate(['email' => 'required|email', 'otp' => 'required']);
        $user = User::where('email', strtolower(trim($request->email)))->first();
        if (!$user) return response()->json(['status' => 'error', 'message' => 'User tidak ditemukan'], 404);

        $otpRecord = OtpCode::where('user_id', $user->user_id)->where('otp_code', $request->otp)->first();
        if (!$otpRecord || Carbon::parse($otpRecord->expired_at)->isPast()) {
            return response()->json(['status' => 'error', 'message' => 'OTP salah atau kadaluarsa'], 401);
        }

        $user->update(['is_verified' => true]);
        $otpRecord->delete();
        return response()->json(['status' => 'success', 'message' => 'Akun berhasil aktif!']);
    }

    /**
     * 3️⃣ LOGIN
     */
    public function login(Request $request): JsonResponse {
        $request->validate(['username' => 'required|string', 'password' => 'required|string']);
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
     * 9️⃣ UPDATE PROFIL
     */
    public function updateProfile(Request $request): JsonResponse {
        $user = Auth::user();
        if (!$user) return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);

        // Mapping Data dari HP
        $nisn = $request->nisn ?? $request->nisn_siswa;
        $nama_ortu = $request->nama_orang_tua ?? $request->parent_name;
        $alamat = $request->alamat_sekolah ?? $request->school ?? $request->alamat;
        $wa_ortu = $request->whatsapp_orang_tua ?? $request->parent_phone;
        $tgl_lahir = $request->tanggal_lahir ?? $request->birth_date;

        try {
            Student::updateOrCreate(
                ['user_id' => $user->user_id],
                [
                    'nisn'          => $nisn,
                    'parent_name'   => $nama_ortu,
                    'school'        => $alamat,
                    'parent_phone'  => $wa_ortu,
                    'birth_date'    => $tgl_lahir,
                ]
            );

            $updatedUser = User::with('student', 'role')->find($user->user_id);
            return response()->json(['status' => 'success', 'message' => 'Profil diperbarui!', 'user' => $updatedUser]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * 🔟 CEK STATUS PROFIL (SINKRON DENGAN DATABASE)
     */
    public function checkClassStatus(Request $request): JsonResponse {
        $user = Auth::user();
        $student = Student::where('user_id', $user->user_id)->first();

        // 1. Cek Kelengkapan Profil (Logika Fleksibel)
        $isComplete = (
            $student && 
            $student->parent_name && 
            $student->school && 
            $student->parent_phone && 
            $student->nisn && 
            $student->birth_date
        );

        if (!$isComplete) {
            return response()->json([
                'status' => 'incomplete',
                'message' => 'Profil belum lengkap',
                'debug_data' => $student // Untuk melihat mana yang kosong
            ]);
        }

        // 2. Cek Pendaftaran (Gunakan program_id, bukan class_id!)
        $isEnrolled = Enrollment::where('user_id', $user->user_id)
                                ->where('program_id', $request->class_id) 
                                ->first();

        return response()->json([
            'status' => 'success',
            'is_enrolled' => $isEnrolled ? true : false,
            'enrollment_status' => $isEnrolled ? $isEnrolled->status : null
        ]);
    }

    /**
     * 1️⃣1️⃣ JOIN CLASS (SINKRON DENGAN DATABASE)
     */
    public function joinClass(Request $request): JsonResponse {
        $user = Auth::user();
        try {
            // Gunakan program_id sesuai migrasi kita
            Enrollment::create([
                'user_id' => $user->user_id,
                'program_id' => $request->class_id,
                'status' => 'pending', 
                'enrolled_at' => now()
            ]);
            return response()->json(['status' => 'success', 'message' => 'Berhasil daftar!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * 1️⃣2️⃣ GET CONTENT (SINKRON DENGAN DATABASE)
     */
    public function getClassContent(Request $request): JsonResponse {
        // Gunakan program_id sesuai migrasi
        $materials = Material::where('program_id', $request->class_id)
                             ->orderBy('created_at', 'desc')
                             ->get();

        return response()->json(['status' => 'success', 'data' => $materials]);
    }
}
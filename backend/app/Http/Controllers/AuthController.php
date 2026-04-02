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
     * 2️⃣ VERIFIKASI OTP
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

        DB::table('users')->where('user_id', $user->user_id)->update([
            'is_verified' => true,
            'updated_at' => now()
        ]);

        $otpRecord->delete();
        return response()->json(['status' => 'success', 'message' => 'Akun berhasil aktif!']);
    }

    /**
     * 3️⃣ LOGIN
     */
    public function login(Request $request): JsonResponse {
        $request->validate([
            'username' => 'required|string', 
            'password' => 'required|string'
        ]);

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

    /**
     * 9️⃣ UPDATE PROFIL (VERSI FINAL - FIX ALAMAT & WHATSAPP)
     */
    public function updateProfile(Request $request): JsonResponse {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        // --- MAPPING OTOMATIS (Mencegah error 'Field is Required') ---
        
        // 1. NISN
        $nisn = $request->nisn ?? $request->nisn_siswa ?? $request->nisnSiswa;

        // 2. Nama Orang Tua
        $nama_ortu = $request->nama_orang_tua ?? $request->parent_name ?? $request->nama_ortu ?? $request->namaOrangTua;

        // 3. ALAMAT SEKOLAH (Penyebab error di screenshot Anda)
        // Kita cek semua kemungkinan nama kunci yang dikirim oleh HP
        $alamat = $request->alamat_sekolah ?? $request->school ?? $request->alamat ?? 
                  $request->asal_sekolah ?? $request->address ?? $request->location;

        // 4. WhatsApp Orang Tua
        $wa_ortu = $request->whatsapp_orang_tua ?? $request->parent_phone ?? $request->wa_ortu ?? 
                   $request->whatsapp_ortu ?? $request->whatsappOrangTua ?? $request->wa_orang_tua;

        // 5. Tanggal Lahir
        $tgl_lahir = $request->tanggal_lahir ?? $request->birth_date ?? $request->dob ?? $request->tgl_lahir;

        // Masukkan data yang sudah ditangkap kembali ke dalam Request agar divalidasi dengan benar
        $request->merge([
            'nisn'               => $nisn,
            'nama_orang_tua'     => $nama_ortu,
            'alamat_sekolah'     => $alamat,
            'whatsapp_orang_tua' => $wa_ortu,
            'tanggal_lahir'      => $tgl_lahir,
        ]);

        // Validasi
        $v = Validator::make($request->all(), [
            'nisn'               => 'required',
            'nama_orang_tua'     => 'required',
            'alamat_sekolah'     => 'required', // Sekarang ini akan terisi oleh variabel $alamat di atas
            'whatsapp_orang_tua' => 'required',
            'tanggal_lahir'      => 'required',
        ]);

        if ($v->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $v->errors()->first(),
                'debug_data_dari_hp' => $request->all() // Jika masih error, cek nama field di sini
            ], 422);
        }

        try {
            // Update tabel students
            \App\Models\Student::updateOrCreate(
                ['user_id' => $user->user_id],
                [
                    'nisn'          => $request->nisn,
                    'parent_name'   => $request->nama_orang_tua,
                    'school'        => $request->alamat_sekolah, // Menyimpan "silaen" ke kolom school
                    'parent_phone'  => $request->whatsapp_orang_tua,
                    'birth_date'    => $request->tanggal_lahir,
                ]
            );

            // Ambil data user terbaru untuk refresh di HP
            $updatedUser = \App\Models\User::with('student', 'role')->find($user->user_id);

            return response()->json([
                'status' => 'success',
                'message' => 'Profil berhasil diperbarui!',
                'user' => $updatedUser
            ]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal simpan: ' . $e->getMessage()], 500);
        }
    }
    /**
     * 🔟 CEK STATUS PROFIL & PENDAFTARAN KELAS
     */
    public function checkClassStatus(Request $request): JsonResponse {
        $user = Auth::user();
        $student = Student::where('user_id', $user->user_id)->first();

        // 1. Cek Kelengkapan Profil
        $isComplete = (
            $student && 
            $student->parent_name && $student->parent_name !== '-' &&
            $student->school && $student->school !== '-' &&
            $student->parent_phone && $student->parent_phone !== '-' &&
            $student->nisn && $student->birth_date
        );

        if (!$isComplete) {
            return response()->json([
                'status' => 'incomplete',
                'message' => 'Profil belum lengkap'
            ]);
        }

        // 2. Cek apakah sudah terdaftar di kelas yang diminta
        $isEnrolled = Enrollment::where('user_id', $user->user_id)
                                ->where('class_id', $request->class_id)
                                ->first();

        return response()->json([
            'status' => 'success',
            'is_enrolled' => $isEnrolled ? true : false,
            'enrollment_status' => $isEnrolled ? $isEnrolled->status : null
        ]);
    }

    /**
     * 1️⃣1️⃣ SISWA DAFTAR KE KELAS (JOIN CLASS)
     */
    public function joinClass(Request $request): JsonResponse {
        $user = Auth::user();
        $request->validate(['class_id' => 'required']);

        try {
            $existing = Enrollment::where('user_id', $user->user_id)
                                  ->where('class_id', $request->class_id)
                                  ->first();

            if ($existing) {
                return response()->json(['status' => 'error', 'message' => 'Anda sudah terdaftar di kelas ini.'], 400);
            }

            Enrollment::create([
                'user_id' => $user->user_id,
                'class_id' => $request->class_id,
                'status' => 'pending', // Menunggu verifikasi admin
                'enrolled_at' => now()
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Pendaftaran berhasil! Admin akan segera memproses akun Anda.'
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * 1️⃣2️⃣ AMBIL MATERI/VIDEO BERDASARKAN KELAS
     */
    public function getClassContent(Request $request): JsonResponse {
        $request->validate(['class_id' => 'required']);

        // Ambil materi yang sesuai dengan program kelas
        $materials = Material::where('class_id', $request->class_id)
                             ->orderBy('created_at', 'desc')
                             ->get();

        return response()->json([
            'status' => 'success',
            'data' => $materials
        ]);
    }
}
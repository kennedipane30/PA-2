<?php

namespace App\Http\Controllers;

// Import semua Model yang dibutuhkan sesuai ERD
use App\Models\{User, Student, OtpCode, Enrollment, Material, Schedule};
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Support\Facades\{Hash, Validator, DB, Auth};
use Carbon\Carbon;

class AuthController extends Controller {

    /**
     * 1. REGISTRASI SISWA
     */
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
                'name' => trim($request->name),
                'email' => $request->email,
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

            $otp = rand(100000, 999999);
            OtpCode::updateOrCreate(['user_id' => $user->usersID], ['otp' => $otp, 'valid_until' => Carbon::now()->addMinutes(10)]);

            DB::commit();
            return response()->json(['status' => 'success', 'otp' => $otp, 'name' => $user->name], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * 2. VERIFIKASI OTP REGISTRASI
     */
    public function verifyRegistration(Request $request): JsonResponse {
        $user = User::where('name', trim($request->name))->first();
        if (!$user) return response()->json(['status' => 'error', 'message' => 'User tidak ditemukan'], 404);

        $otpRecord = OtpCode::where('user_id', $user->usersID)->where('otp', $request->otp)->where('valid_until', '>', now())->first();
        if (!$otpRecord) return response()->json(['status' => 'error', 'message' => 'Kode OTP Salah/Kadaluarsa'], 401);

        $user->is_verified = true;
        $user->save();
        $otpRecord->delete();

        return response()->json(['status' => 'success', 'message' => 'Akun Aktif! Silakan Login']);
    }

    /**
     * 3. LOGIN SISWA
     */
    public function login(Request $request): JsonResponse {
        $user = User::where('name', trim($request->name))->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['status' => 'error', 'message' => 'Nama atau Password Salah'], 401);
        }
        if (!$user->is_verified) {
            return response()->json(['status' => 'error', 'message' => 'Akun belum verifikasi WA!'], 403);
        }
        return response()->json([
            'status' => 'success',
            'token' => $user->createToken('token')->plainTextToken,
            'user' => $user->load('student')
        ]);
    }

    /**
     * 4. LENGKAPI PROFIL
     */
    public function updateProfile(Request $request): JsonResponse {
        $v = Validator::make($request->all(), [
            'parent_name' => 'required|string|max:255',
            'alamat' => 'required|string',
            'wa_ortu' => 'required|numeric',
            'nisn' => 'required|numeric',
            'dob' => 'required|date',
        ]);
        if ($v->fails()) return response()->json(['status' => 'error', 'message' => $v->errors()->first()], 422);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->student->update([
            'parent_name' => $request->parent_name,
            'school' => $request->alamat,
            'wa_ortu' => $request->wa_ortu,
            'nisn' => $request->nisn,
            'dob' => $request->dob,
        ]);
        return response()->json(['status' => 'success', 'message' => 'Profil berhasil dilengkapi']);
    }

    /**
     * 5. DAFTAR KELAS (UPLOAD BUKTI)
     */
    public function joinClass(Request $request): JsonResponse {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $s = $user->student;

        if ($s->parent_name == "-" || $s->school == "-" || $s->dob == null) {
            return response()->json(['status' => 'error', 'message' => 'Lengkapi Profil dulu!'], 403);
        }

        $path = $request->file('payment_proof')->store('proofs', 'public');
        Enrollment::create([
            'user_id' => $user->usersID,
            'class_id' => $request->class_id,
            'payment_proof' => $path,
            'status' => 'pending'
        ]);
        return response()->json(['status' => 'success', 'message' => 'Pendaftaran terkirim!']);
    }

    /**
     * 6. CEK STATUS KELAS
     */
    public function checkClassStatus(Request $request): JsonResponse {
        $enroll = Enrollment::where('user_id', Auth::id())->where('class_id', $request->class_id)->first();
        return response()->json(['status' => $enroll ? $enroll->status : 'none']);
    }

    /**
     * 7. AMBIL KONTEN MATERI PER KELAS
     */
    public function getClassContent(Request $request): JsonResponse {
        $classId = $request->class_id;
        $materi = Material::where('class_id', $classId)->get();
        $enroll = Enrollment::where('user_id', Auth::id())->where('class_id', $classId)->first();

        return response()->json([
            'status' => 'success',
            'enroll_status' => $enroll ? $enroll->status : 'none',
            'price' => '900.000',
            'duration' => '30 Hari',
            'materi' => $materi
        ]);
    }

    /**
     * 8. AMBIL JADWAL BELAJAR (MODIFIKASI: Tambahkan Nama Kelas)
     */
    public function getSiswaSchedule(Request $request): JsonResponse {
        $user = Auth::user();

        // Ambil ID kelas yang statusnya 'aktif'
        $activeClassIds = Enrollment::where('user_id', $user->usersID)
                                    ->where('status', 'aktif')
                                    ->pluck('class_id');

        // MODIFIKASI: Memuat relasi teacher DAN classModel
        $schedules = Schedule::whereIn('class_id', $activeClassIds)
                            ->with(['teacher', 'classModel'])
                            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $schedules
        ]);
    }

    /**
     * 9. LOGOUT
     */
    public function logout(Request $request): JsonResponse {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['status' => 'success', 'message' => 'Berhasil Logout']);
    }
}

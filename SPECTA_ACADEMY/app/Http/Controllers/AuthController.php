<?php
namespace App\Http\Controllers;

use App\Models\{User, Student, OtpCode, Enrollment, Material};
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Support\Facades\{Hash, Validator, DB, Auth};
use Carbon\Carbon;

class AuthController extends Controller {

    // 1. REGISTRASI SISWA
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

            // Buat profil student default
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
        } catch (\Exception $e) { DB::rollBack(); return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500); }
    }

    // 2. VERIFIKASI OTP REGISTRASI (PERBAIKAN DATA PERSISTENCE)
    public function verifyRegistration(Request $request): JsonResponse {
        // Gunakan trim untuk menghindari eror karena spasi
        $user = User::where('name', trim($request->name))->first();

        if (!$user) return response()->json(['status' => 'error', 'message' => 'User tidak ditemukan'], 404);

        $otpRecord = OtpCode::where('user_id', $user->usersID)
                            ->where('otp', $request->otp)
                            ->where('valid_until', '>', now())
                            ->first();

        if (!$otpRecord) return response()->json(['status' => 'error', 'message' => 'Kode OTP Salah/Kadaluarsa'], 401);

        // MODIFIKASI: Pastikan save() dipanggil untuk memperbarui database
        $user->is_verified = true;
        $user->save();

        $otpRecord->delete();

        return response()->json(['status' => 'success', 'message' => 'Akun Aktif! Silakan Login']);
    }

    // 3. LOGIN (Nama & Password)
    public function login(Request $request): JsonResponse {
        // Gunakan trim agar input nama lebih akurat
        $user = User::where('name', trim($request->name))->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['status' => 'error', 'message' => 'Nama atau Password Salah'], 401);
        }

        // Pastikan pengecekan boolean benar
        if (!$user->is_verified) {
            return response()->json(['status' => 'error', 'message' => 'Akun belum verifikasi WA!'], 403);
        }

        return response()->json([
            'status' => 'success',
            'token' => $user->createToken('token')->plainTextToken,
            'user' => $user->load('student')
        ]);
    }

    // 4. LENGKAPI PROFIL
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

    // 5. DAFTAR KELAS
    public function joinClass(Request $request): JsonResponse {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $s = $user->student;

        if ($s->parent_name == "-" || $s->school == "-" || $s->dob == null) {
            return response()->json(['status' => 'error', 'message' => 'Lengkapi Profil di menu Akun terlebih dahulu!'], 403);
        }

        $v = Validator::make($request->all(), [
            'class_id' => 'required',
            'payment_proof' => 'required|image|max:2048',
        ]);
        if ($v->fails()) return response()->json(['status' => 'error', 'message' => 'Bukti transfer wajib diunggah'], 422);

        $path = $request->file('payment_proof')->store('proofs', 'public');

        Enrollment::create([
            'user_id' => $user->usersID,
            'class_id' => $request->class_id,
            'payment_proof' => $path,
            'status' => 'pending'
        ]);

        return response()->json(['status' => 'success', 'message' => 'Pendaftaran berhasil dikirim!']);
    }

    // 6. CEK STATUS KELAS
    public function checkClassStatus(Request $request): JsonResponse {
        $enroll = Enrollment::where('user_id', Auth::id())->where('class_id', $request->class_id)->first();
        return response()->json(['status' => $enroll ? $enroll->status : 'none']);
    }

    public function getClassContent(Request $request): JsonResponse {
    $classId = $request->class_id;
    $materi = Material::where('class_id', $classId)->get();

    // Cek apakah siswa ini sudah terdaftar/aktif di kelas ini
    $enroll = Enrollment::where('user_id', Auth::id())->where('class_id', $classId)->first();

    return response()->json([
        'status' => 'success',
        'enroll_status' => $enroll ? $enroll->status : 'none', // none, pending, aktif
        'price' => '900.000',
        'duration' => '30 Hari',
        'materi' => $materi
    ]);
}

    // 7. LOGOUT
    public function logout(Request $request): JsonResponse {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['status' => 'success', 'message' => 'Berhasil Logout']);
    }
}

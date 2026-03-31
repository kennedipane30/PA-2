<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Profile;
use App\Models\OtpCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'alamat' => 'required|string',
            'nama_ibu' => 'required|string|max:255',
            'hp_siswa' => 'required|string',
            'hp_ortu' => 'required|string',
            'hp_ortu_2' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Get student role
            $studentRole = Role::where('nama_role', 'student')->first();
            
            if (!$studentRole) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student role not found'
                ], 500);
            }

            // Create user
            $user = User::create([
                'role_id' => $studentRole->id,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'is_active' => false, // Will be activated after OTP verification
                'email_verified' => false,
            ]);

            // Create profile
            Profile::create([
                'user_id' => $user->id,
                'nomor_wa' => $request->hp_siswa,
                'alamat' => $request->alamat,
                'nama_ibu' => $request->nama_ibu,
                'nomor_wa_ortu' => $request->hp_ortu,
                'nomor_wa_ortu_2' => $request->hp_ortu_2,
            ]);

            // Generate OTP
            $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            
            OtpCode::create([
                'user_id' => $user->id,
                'otp_code' => Hash::make($otp),
                'expires_at' => now()->addMinutes(5),
                'is_used' => false,
            ]);

            // TODO: Send OTP via WhatsApp
            // For now, return OTP in response (ONLY FOR DEVELOPMENT!)
            
            return response()->json([
                'success' => true,
                'message' => 'Registration successful. Please verify OTP.',
                'data' => [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'otp' => $otp, // REMOVE THIS IN PRODUCTION!
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials'
                ], 401);
            }

            // Generate OTP for 2FA
            $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            
            OtpCode::create([
                'user_id' => $user->id,
                'otp_code' => Hash::make($otp),
                'expires_at' => now()->addMinutes(5),
                'is_used' => false,
            ]);

            // TODO: Send OTP via WhatsApp
            
            return response()->json([
                'success' => true,
                'message' => 'Login successful. Please verify OTP.',
                'data' => [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'otp' => $otp, // REMOVE THIS IN PRODUCTION!
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Get latest unused OTP
            $otpRecord = OtpCode::where('user_id', $user->id)
                ->where('is_used', false)
                ->where('expires_at', '>', now())
                ->latest()
                ->first();

            if (!$otpRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'OTP expired or not found'
                ], 400);
            }

            if (!Hash::check($request->otp, $otpRecord->otp_code)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid OTP'
                ], 400);
            }

            // Mark OTP as used
            $otpRecord->update([
                'is_used' => true,
                'used_at' => now(),
            ]);

            // Activate user
            $user->update([
                'is_active' => true,
                'email_verified' => true,
                'email_verified_at' => now(),
            ]);

            // Create token (using Sanctum)
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'OTP verified successfully',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role->nama_role ?? null,
                    ],
                    'token' => $token,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'OTP verification failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function me(Request $request)
    {
        try {
            $user = $request->user()->load(['role', 'profile']);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role->nama_role ?? null,
                    'profile' => $user->profile,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get user data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

import 'dart:convert';
import 'package:http/http.dart' as http;

class AuthService {
  // Gunakan 10.0.2.2 untuk Emulator Android, atau IP asli Laptop jika pakai HP Fisik
  static const String baseUrl = 'http://10.0.2.2:8000/api';

  // 1. REGISTRASI SISWA
  // Mengirim data: name, email, nomor_wa, password, password_confirmation
  static Future<http.Response> register(Map<String, dynamic> data) async {
    return await http.post(
      Uri.parse('$baseUrl/register'), 
      headers: {'Accept': 'application/json'}, 
      body: data
    );
  }

  // 2. VERIFIKASI REGISTRASI (AKTIVASI AKUN)
  // Dipanggil setelah Daftar untuk mengubah is_verified menjadi true
  static Future<http.Response> verifyRegistration(String name, String otp) async {
    return await http.post(
      Uri.parse('$baseUrl/verify-registration'), 
      headers: {'Accept': 'application/json'}, 
      body: {
        'name': name, 
        'otp': otp
      }
    );
  }

  // 3. LOGIN SISWA (Hanya Nama dan Password)
  // Akun harus sudah is_verified = true agar bisa tembus
  static Future<http.Response> login(String name, String password) async {
    return await http.post(
      Uri.parse('$baseUrl/login'), 
      headers: {'Accept': 'application/json'}, 
      body: {
        'name': name,     
        'password': password
      }
    );
  }

  // 4. AMBIL PROFIL USER (Wajib bawa Token)
  static Future<Map<String, dynamic>?> getUserProfile(String token) async {
    final response = await http.get(
      Uri.parse('$baseUrl/user'),
      headers: {
        'Accept': 'application/json',
        'Authorization': 'Bearer $token',
      },
    );

    if (response.statusCode == 200) {
      return jsonDecode(response.body);
    }
    return null;
  }

  // 5. LENGKAPI PROFIL (Update Nama Ortu, Alamat, WA Ortu)
  static Future<http.Response> updateProfile(Map<String, dynamic> data, String token) async {
    return await http.post(
      Uri.parse('$baseUrl/update-profile'),
      headers: {
        'Accept': 'application/json',
        'Authorization': 'Bearer $token',
      },
      body: data,
    );
  }

  // 6. CEK STATUS PENDAFTARAN KELAS (none/pending/aktif)
  static Future<http.Response> checkClassStatus(int classId, String token) async {
    return await http.post(
      Uri.parse('$baseUrl/class/check-status'),
      headers: {
        'Accept': 'application/json',
        'Authorization': 'Bearer $token',
      },
      body: {
        'class_id': classId.toString()
      },
    );
  }

  // 7. DAFTARKAN SISWA KE KELAS (Status awal: pending)
  static Future<http.Response> joinClass(int classId, String token) async {
    return await http.post(
      Uri.parse('$baseUrl/class/join'),
      headers: {
        'Accept': 'application/json',
        'Authorization': 'Bearer $token',
      },
      body: {
        'class_id': classId.toString()
      },
    );
  }

  // 8. VERIFIKASI OTP LOGIN (Jika di masa depan butuh 2FA saat login)
  static Future<http.Response> verifyOtp(String email, String otp) async {
    return await http.post(
      Uri.parse('$baseUrl/verify-otp'), 
      headers: {'Accept': 'application/json'}, 
      body: {
        'email': email, 
        'otp': otp
      }
    );
  }
}
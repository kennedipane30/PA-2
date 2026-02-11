import 'dart:convert';
import 'package:http/http.dart' as http;

class AuthService {
  // Gunakan 10.0.2.2 untuk Emulator Android, atau IP Laptop jika pakai HP Fisik
  static const String baseUrl = 'http://10.0.2.2:8000/api';

  // 1. REGISTRASI SISWA
  static Future<http.Response> register(Map<String, dynamic> data) async {
    return await http.post(
      Uri.parse('$baseUrl/register'), 
      headers: {'Accept': 'application/json'}, 
      body: data
    );
  }

  // 2. LOGIN (Step 1: Cek Password)
  static Future<http.Response> login(String email, String password) async {
    return await http.post(
      Uri.parse('$baseUrl/login'), 
      headers: {'Accept': 'application/json'}, 
      body: {'email': email, 'password': password}
    );
  }

  // 3. VERIFIKASI OTP (Step 2: Dapatkan Token)
  static Future<http.Response> verifyOtp(String email, String otp) async {
    return await http.post(
      Uri.parse('$baseUrl/verify-otp'), 
      headers: {'Accept': 'application/json'}, 
      body: {'email': email, 'otp': otp}
    );
  }

  // 4. AMBIL PROFIL USER (Menggunakan Token)
  static Future<Map<String, dynamic>?> getUserProfile(String token) async {
    final response = await http.get(
      Uri.parse('$baseUrl/user'),
      headers: {
        'Accept': 'application/json',
        'Authorization': 'Bearer $token', // Membawa Token Keamanan
      },
    );

    if (response.statusCode == 200) {
      return jsonDecode(response.body);
    }
    return null;
  }

  // --- FITUR PENDAFTARAN KELAS (BARU) ---

  // 5. CEK STATUS PENDAFTARAN KELAS
  // Digunakan untuk tahu apakah siswa sudah daftar (none/pending/aktif)
  static Future<http.Response> checkClassStatus(int classId, String token) async {
    return await http.post(
      Uri.parse('$baseUrl/class/check-status'),
      headers: {
        'Accept': 'application/json',
        'Authorization': 'Bearer $token',
      },
      body: {'class_id': classId.toString()},
    );
  }

  // 6. DAFTARKAN SISWA KE KELAS
  // Digunakan saat siswa klik tombol "Daftar Sekarang"
  static Future<http.Response> joinClass(int classId, String token) async {
    return await http.post(
      Uri.parse('$baseUrl/class/join'),
      headers: {
        'Accept': 'application/json',
        'Authorization': 'Bearer $token',
      },
      body: {'class_id': classId.toString()},
    );
  }
}
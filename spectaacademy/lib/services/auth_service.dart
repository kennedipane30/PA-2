import 'dart:convert';
import 'package:http/http.dart' as http;

class AuthService {
  // Gunakan 10.0.2.2 untuk Emulator Android, atau IP asli Laptop jika pakai HP Fisik
  static const String baseUrl = 'http://10.0.2.2:8000/api';

  // 1. REGISTRASI SISWA
  // Menerima data lengkap (Nama, Email, WA, Password, dll)
  static Future<http.Response> register(Map<String, dynamic> data) async {
    return await http.post(
      Uri.parse('$baseUrl/register'), 
      headers: {'Accept': 'application/json'}, 
      body: data
    );
  }

  // 2. VERIFIKASI SETELAH PENDAFTARAN (Aktivasi Akun)
  // Dipanggil di halaman OTP setelah siswa klik Daftar
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
  // MODIFIKASI: Menggunakan parameter 'name' sesuai bimbingan terbaru
  static Future<http.Response> login(String name, String password) async {
    return await http.post(
      Uri.parse('$baseUrl/login'), 
      headers: {'Accept': 'application/json'}, 
      body: {
        'name': name,     // Menggunakan Nama Lengkap untuk login
        'password': password
      }
    );
  }

  // --- FUNGSI DI BAWAH INI TETAP UTUH SESUAI PERMINTAAN ---

  // 4. VERIFIKASI OTP (Opsional - Jika masih dibutuhkan di alur lain)
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

  // 5. AMBIL PROFIL USER (Menggunakan Token Bearer)
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

  // 6. CEK STATUS PENDAFTARAN KELAS
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

  // 7. DAFTARKAN SISWA KE KELAS
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
}
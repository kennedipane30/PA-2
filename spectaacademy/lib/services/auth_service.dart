import 'dart:convert';
import 'dart:io'; // Penting untuk File bukti transfer
import 'package:http/http.dart' as http;

class AuthService {
  // Gunakan 10.0.2.2 untuk Emulator Android, atau IP asli Laptop jika pakai HP Fisik
  static const String baseUrl = 'http://10.0.2.2:8000/api';

  // 1. REGISTRASI SISWA
  static Future<http.Response> register(Map<String, dynamic> data) async {
    return await http.post(
      Uri.parse('$baseUrl/register'), 
      headers: {'Accept': 'application/json'}, 
      body: data
    );
  }

  // 2. VERIFIKASI REGISTRASI (AKTIVASI AKUN)
  static Future<http.Response> verifyRegistration(String name, String otp) async {
    return await http.post(
      Uri.parse('$baseUrl/verify-registration'), 
      headers: {'Accept': 'application/json'}, 
      body: {'name': name, 'otp': otp}
    );
  }

  // 3. LOGIN SISWA (Hanya Nama dan Password)
  static Future<http.Response> login(String name, String password) async {
    return await http.post(
      Uri.parse('$baseUrl/login'), 
      headers: {'Accept': 'application/json'}, 
      body: {'name': name, 'password': password}
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

  // 5. LENGKAPI PROFIL (Update Nama Ortu, Alamat, NISN, Tgl Lahir)
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

  // 6. AMBIL KONTEN MATERI DINAMIS
  static Future<http.Response> getClassContent(int classId, String token) async {
    return await http.post(
      Uri.parse('$baseUrl/class/content'),
      headers: {
        'Accept': 'application/json',
        'Authorization': 'Bearer $token',
      },
      body: {'class_id': classId.toString()},
    );
  }

  // 7. CEK STATUS PENDAFTARAN KELAS
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

  // 8. DAFTARKAN SISWA KE KELAS DENGAN UPLOAD GAMBAR (MULTIPART)
  // Perbaikan: Hanya satu fungsi joinClass yang bersih
  static Future<http.StreamedResponse> joinClass(int classId, String filePath, String token) async {
    var request = http.MultipartRequest('POST', Uri.parse('$baseUrl/class/join'));
    
    request.headers.addAll({
      'Accept': 'application/json',
      'Authorization': 'Bearer $token',
    });

    request.fields['class_id'] = classId.toString();
    
    // Menambahkan file bukti transfer
    request.files.add(await http.MultipartFile.fromPath('payment_proof', filePath));
    
    return await request.send();
  }

  // 9. LOGOUT
  static Future<http.Response> logout(String token) async {
    return await http.post(
      Uri.parse('$baseUrl/logout'),
      headers: {
        'Accept': 'application/json',
        'Authorization': 'Bearer $token',
      },
    );
  }
}
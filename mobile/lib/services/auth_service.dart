import 'dart:convert';
import 'package:http/http.dart' as http;

class AuthService {
  // Gunakan 10.0.2.2 untuk Emulator Android, atau IP asli Laptop jika pakai HP fisik
  static const String baseUrl = 'http://10.0.2.2:8000/api';

  // Helper untuk Header Standar
  static Map<String, String> _headers([String? token]) {
    var h = {
      'Accept': 'application/json',
    };
    if (token != null) {
      h['Authorization'] = 'Bearer $token';
    }
    return h;
  }

  // --- 1. AUTHENTICATION & PROFILE ---
  
  // Registrasi Siswa
  static Future<http.Response> register(Map<String, dynamic> data) async => 
      await http.post(Uri.parse('$baseUrl/register'), headers: _headers(), body: data);

  // Verifikasi OTP: Mengirim 'email' dan 'otp' (Sudah Sinkron dengan Backend)
  static Future<http.Response> verifyRegistration(String email, String otp) async => 
      await http.post(Uri.parse('$baseUrl/verify-registration'), headers: _headers(), body: {
        'email': email, 
        'otp': otp
      });

  // Login: Menggunakan 'email' (Bukan 'name')
  static Future<http.Response> login(String email, String password) async => 
      await http.post(Uri.parse('$baseUrl/login'), headers: _headers(), body: {
        'email': email, 
        'password': password
      });

  static Future<http.Response> updateProfile(Map<String, dynamic> data, String token) async => 
      await http.post(Uri.parse('$baseUrl/update-profile'), headers: _headers(token), body: data);
  
  static Future<http.Response> logout(String token) async => 
      await http.post(Uri.parse('$baseUrl/logout'), headers: _headers(token));

  // --- 2. MANAJEMEN KELAS & JADWAL ---
  static Future<http.Response> getClassContent(int classId, String token) async => 
      await http.post(Uri.parse('$baseUrl/class/content'), headers: _headers(token), body: {'class_id': classId.toString()});
  
  static Future<http.Response> checkClassStatus(int classId, String token) async => 
      await http.post(Uri.parse('$baseUrl/class/check-status'), headers: _headers(token), body: {'class_id': classId.toString()});
  
  static Future<http.StreamedResponse> joinClass(int classId, String filePath, String token) async {
    var req = http.MultipartRequest('POST', Uri.parse('$baseUrl/class/join'))
      ..headers.addAll(_headers(token))
      ..fields['class_id'] = classId.toString()
      ..files.add(await http.MultipartFile.fromPath('payment_proof', filePath));
    return await req.send();
  }
  
  static Future<http.Response> getSiswaSchedule(String token) async => 
      await http.get(Uri.parse('$baseUrl/schedules'), headers: _headers(token));

  // --- 3. EVALUASI & TRYOUT ---
  static Future<http.Response> getQuestions(int tryoutId, String token) async => 
      await http.post(Uri.parse('$baseUrl/tryout/questions'), headers: _headers(token), body: {'tryout_id': tryoutId.toString()});
  
  static Future<http.Response> submitTryout({required int tryoutId, required Map<int, String> answers, required String token}) async {
    Map<String, String> stringAnswers = answers.map((key, value) => MapEntry(key.toString(), value));
    return await http.post(
      Uri.parse('$baseUrl/tryout/submit'), 
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'Authorization': 'Bearer $token'
      },
      body: jsonEncode({'tryout_id': tryoutId, 'answers': stringAnswers})
    );
  }

  // --- 4. LUPA PASSWORD ---
  // Menggunakan 'email' sesuai fungsi forgotPassword di Backend
  static Future<http.Response> forgotPassword(String email) async => 
      await http.post(Uri.parse('$baseUrl/forgot-password'), headers: _headers(), body: {'email': email});
  
  static Future<http.Response> resetPassword(Map<String, dynamic> data) async => 
      await http.post(Uri.parse('$baseUrl/reset-password'), headers: _headers(), body: data);

  // --- 5. AKTIVITAS / GALERI ---
  static Future<http.Response> fetchGalleries() async => 
      await http.get(Uri.parse('$baseUrl/galeri'), headers: _headers());
}
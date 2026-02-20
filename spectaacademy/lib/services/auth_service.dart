import 'dart:convert';
import 'dart:io'; // Penting untuk File bukti transfer
import 'package:http/http.dart' as http;

class AuthService {
  static const String baseUrl = 'http://10.0.2.2:8000/api';

  static Future<http.Response> register(Map<String, dynamic> data) async {
    return await http.post(Uri.parse('$baseUrl/register'), headers: {'Accept': 'application/json'}, body: data);
  }

  static Future<http.Response> verifyRegistration(String name, String otp) async {
    return await http.post(Uri.parse('$baseUrl/verify-registration'), headers: {'Accept': 'application/json'}, body: {'name': name, 'otp': otp});
  }

  static Future<http.Response> login(String name, String password) async {
    return await http.post(Uri.parse('$baseUrl/login'), headers: {'Accept': 'application/json'}, body: {'name': name, 'password': password});
  }

  static Future<http.Response> updateProfile(Map<String, dynamic> data, String token) async {
    return await http.post(Uri.parse('$baseUrl/update-profile'), headers: {'Accept': 'application/json', 'Authorization': 'Bearer $token'}, body: data);
  }

  static Future<http.Response> checkClassStatus(int classId, String token) async {
    return await http.post(Uri.parse('$baseUrl/class/check-status'), headers: {'Accept': 'application/json', 'Authorization': 'Bearer $token'}, body: {'class_id': classId.toString()});
  }

  // FUNGSI DAFTAR KELAS DENGAN UPLOAD GAMBAR (MULTIPART)
  static Future<http.StreamedResponse> joinClass(int classId, String filePath, String token) async {
    var request = http.MultipartRequest('POST', Uri.parse('$baseUrl/class/join'));
    request.headers.addAll({
      'Accept': 'application/json',
      'Authorization': 'Bearer $token',
    });
    request.fields['class_id'] = classId.toString();
    request.files.add(await http.MultipartFile.fromPath('payment_proof', filePath));
    return await request.send();
  }
}
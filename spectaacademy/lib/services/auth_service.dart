import 'dart:convert';
import 'package:http/http.dart' as http;

class AuthService {
  static const String baseUrl = 'http://10.0.2.2:8000/api'; // Emulator: 10.0.2.2

  static Future<http.Response> register(Map<String, dynamic> data) async {
    return await http.post(Uri.parse('$baseUrl/register'), headers: {'Accept': 'application/json'}, body: data);
  }

  static Future<http.Response> login(String email, String password) async {
    return await http.post(Uri.parse('$baseUrl/login'), headers: {'Accept': 'application/json'}, body: {'email': email, 'password': password});
  }

  static Future<http.Response> verifyOtp(String email, String otp) async {
    return await http.post(Uri.parse('$baseUrl/verify-otp'), headers: {'Accept': 'application/json'}, body: {'email': email, 'otp': otp});
  }
}
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import '../services/auth_service.dart'; // Pastikan path import benar
import 'login_page.dart'; // Pastikan path import benar

class ActivationScreen extends StatefulWidget {
  final String email;

  const ActivationScreen({super.key, required this.email});

  @override
  State<ActivationScreen> createState() => _ActivationScreenState();
}

class _ActivationScreenState extends State<ActivationScreen> {
  final TextEditingController _otpController = TextEditingController();
  final Color spektaRed = const Color(0xFF990000); // Warna khas Spekta
  bool _isLoading = false;

  Future<void> verifyOTP() async {
    if (_otpController.text.length < 6) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text("Masukkan 6 digit kode OTP")),
      );
      return;
    }

    setState(() => _isLoading = true);

    try {
      // Menggunakan AuthService yang sudah kita perbaiki tadi
      final response = await AuthService.verifyRegistration(
        widget.email, 
        _otpController.text
      );

      final data = jsonDecode(response.body);

      if (response.statusCode == 200) {
        if (!mounted) return;
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text("Akun berhasil diaktifkan! Silakan login."),
            backgroundColor: Colors.green,
          ),
        );
        
        // Pindah ke Login dan hapus semua history navigasi
        Navigator.pushAndRemoveUntil(
          context,
          MaterialPageRoute(builder: (context) => const LoginPage()),
          (route) => false,
        );
      } else {
        if (!mounted) return;
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(data['message'] ?? "Kode OTP Salah"), 
            backgroundColor: Colors.red
          ),
        );
      }
    } catch (e) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text("Gagal terhubung ke server. Periksa koneksi Anda."), 
          backgroundColor: Colors.red
        ),
      );
    } finally {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: const Text("Verifikasi Akun", style: TextStyle(fontWeight: FontWeight.bold)),
        backgroundColor: Colors.white,
        foregroundColor: spektaRed,
        elevation: 0,
        centerTitle: true,
      ),
      body: SingleChildScrollView(
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 30.0),
          child: Column(
            children: [
              const SizedBox(height: 40),
              // Icon Ilustrasi
              Container(
                padding: const EdgeInsets.all(20),
                decoration: BoxDecoration(
                  color: spektaRed.withOpacity(0.1),
                  shape: BoxShape.circle,
                ),
                child: Icon(Icons.mark_email_read_rounded, size: 80, color: spektaRed),
              ),
              const SizedBox(height: 30),
              const Text(
                "Cek Email Anda",
                style: TextStyle(fontSize: 24, fontWeight: FontWeight.bold),
              ),
              const SizedBox(height: 15),
              RichText(
                textAlign: TextAlign.center,
                text: TextSpan(
                  style: const TextStyle(fontSize: 16, color: Colors.grey, height: 1.5),
                  children: [
                    const TextSpan(text: "Kami telah mengirimkan kode verifikasi 6-digit ke alamat email:\n"),
                    TextSpan(
                      text: widget.email,
                      style: TextStyle(color: spektaRed, fontWeight: FontWeight.bold),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 40),
              
              // Input OTP yang lebih cantik
              TextField(
                controller: _otpController,
                keyboardType: TextInputType.number,
                maxLength: 6,
                textAlign: TextAlign.center,
                style: const TextStyle(fontSize: 28, letterSpacing: 15, fontWeight: FontWeight.bold),
                decoration: InputDecoration(
                  hintText: "000000",
                  counterText: "", // Sembunyikan counter maxLength
                  hintStyle: TextStyle(color: Colors.grey.shade300, letterSpacing: 15),
                  enabledBorder: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(15),
                    borderSide: BorderSide(color: Colors.grey.shade300),
                  ),
                  focusedBorder: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(15),
                    borderSide: BorderSide(color: spektaRed, width: 2),
                  ),
                ),
              ),
              
              const SizedBox(height: 40),
              
              _isLoading 
                ? CircularProgressIndicator(color: spektaRed)
                : ElevatedButton(
                    onPressed: verifyOTP,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: spektaRed,
                      minimumSize: const Size(double.infinity, 60),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
                      elevation: 2,
                    ),
                    child: const Text(
                      "VERIFIKASI & AKTIFKAN", 
                      style: TextStyle(color: Colors.white, fontSize: 16, fontWeight: FontWeight.bold),
                    ),
                  ),
              const SizedBox(height: 20),
              TextButton(
                onPressed: () {
                  // Tambahkan fungsi resend OTP di sini jika diperlukan nanti
                },
                child: Text(
                  "Tidak menerima email? Kirim ulang",
                  style: TextStyle(color: spektaRed, fontWeight: FontWeight.w600),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
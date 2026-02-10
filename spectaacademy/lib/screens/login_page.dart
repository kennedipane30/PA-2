import 'package:flutter/material.dart';
import '../services/auth_service.dart';
import 'otp_page.dart';
import 'register_page.dart';
import 'dart:convert'; // Penting untuk membaca data dari API

class LoginPage extends StatelessWidget {
  const LoginPage({super.key});

  @override
  Widget build(BuildContext context) {
    // Inisialisasi Controller & Warna
    final TextEditingController emailCtrl = TextEditingController();
    final TextEditingController passCtrl = TextEditingController();
    const Color spektaRed = Color(0xFF990000);

    // FUNGSI HANDLE LOGIN
    void handleLogin() async {
      // Validasi sederhana di sisi UI
      if (emailCtrl.text.isEmpty || passCtrl.text.isEmpty) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text("Email dan Password tidak boleh kosong!"))
        );
        return;
      }

      // Tampilkan Loading (Progress Indicator)
      showDialog(
        context: context, 
        barrierDismissible: false, 
        builder: (context) => const Center(child: CircularProgressIndicator(color: spektaRed))
      );

      // Panggil API Laravel
      var resp = await AuthService.login(emailCtrl.text, passCtrl.text);
      
      // Tutup Loading
      Navigator.pop(context);

      if (resp.statusCode == 200) {
        // Ambil data OTP dari response (MODE SIMULASI)
        final responseData = jsonDecode(resp.body);
        String otpCode = responseData['otp'].toString();

        // Tampilkan Dialog Kode OTP (Khusus masa pengerjaan, tidak untuk seminar)
        showDialog(
          context: context,
          builder: (context) => AlertDialog(
            title: const Text("OTP SIMULASI (Dev Mode)"),
            content: Text("Kode OTP Anda: $otpCode\n\n(Pesan ini muncul karena kita mematikan pengiriman WhatsApp Fonnte sementara)"),
            actions: [
              TextButton(
                onPressed: () {
                  Navigator.pop(context); // Tutup dialog
                  // Pindah ke Halaman OTP
                  Navigator.push(context, MaterialPageRoute(
                    builder: (_) => OtpPage(email: emailCtrl.text)
                  ));
                },
                child: const Text("LANJUTKAN", style: TextStyle(color: spektaRed, fontWeight: FontWeight.bold)),
              )
            ],
          ),
        );
      } else {
        // Jika login gagal (Email/Pass salah)
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(backgroundColor: Colors.red, content: Text("Gmail atau Password Salah!"))
        );
      }
    }

    return Scaffold(
      backgroundColor: Colors.white,
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(35),
        child: Column(
          children: [
            const SizedBox(height: 100),
            // Logo / Judul Spekta
            const Text("SPEKTA", style: TextStyle(fontSize: 45, fontWeight: FontWeight.bold, color: spektaRed, letterSpacing: 2)),
            const Text("ACADEMY", style: TextStyle(fontSize: 14, letterSpacing: 8, color: Colors.grey)),
            const SizedBox(height: 60),

            // Input Email
            TextField(
              controller: emailCtrl,
              keyboardType: TextInputType.emailAddress,
              decoration: InputDecoration(
                labelText: "Gmail / Email",
                prefixIcon: const Icon(Icons.email_outlined, color: spektaRed),
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(15)),
              ),
            ),
            const SizedBox(height: 20),

            // Input Password
            TextField(
              controller: passCtrl,
              obscureText: true,
              decoration: InputDecoration(
                labelText: "Password",
                prefixIcon: const Icon(Icons.lock_outline, color: spektaRed),
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(15)),
              ),
            ),
            const SizedBox(height: 40),

            // Tombol Login
            ElevatedButton(
              style: ElevatedButton.styleFrom(
                backgroundColor: spektaRed,
                minimumSize: const Size(double.infinity, 55),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
                elevation: 5
              ),
              onPressed: handleLogin,
              child: const Text("MASUK KE DASHBOARD", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
            ),

            const SizedBox(height: 20),

            // Tombol Ke Halaman Register
            TextButton(
              onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const RegisterPage())),
              child: const Text("Belum punya akun? Daftar Sekarang", style: TextStyle(color: Colors.grey, fontWeight: FontWeight.bold)),
            )
          ],
        ),
      ),
    );
  }
}
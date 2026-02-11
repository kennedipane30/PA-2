import 'package:flutter/material.dart';
import '../services/auth_service.dart';
import 'main_screen.dart'; 
import 'dart:convert'; 

class OtpPage extends StatelessWidget {
  final String email;
  const OtpPage({super.key, required this.email});

  @override
  Widget build(BuildContext context) {
    final otpCtrl = TextEditingController();
    const Color spektaRed = Color(0xFF990000);

    void handleVerify() async {
      if (otpCtrl.text.isEmpty) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text("Masukkan kode OTP terlebih dahulu"))
        );
        return;
      }

      showDialog(
        context: context, 
        barrierDismissible: false, 
        builder: (context) => const Center(child: CircularProgressIndicator(color: spektaRed))
      );

      var resp = await AuthService.verifyOtp(email, otpCtrl.text);
      
      // Keamanan: Cek apakah context masih aktif setelah await (Syarat Kualitas Perangkat Lunak)
      if (!context.mounted) return;
      Navigator.pop(context); // Tutup Loading

      if (resp.statusCode == 200) {
        final data = jsonDecode(resp.body);
        
        String nameFromDb = data['user']['name'] ?? "Siswa Spekta";
        String tokenFromDb = data['token']; // AMBIL TOKEN DARI LARAVEL

        Navigator.pushAndRemoveUntil(
          context,
          MaterialPageRoute(builder: (_) => MainScreen(
            userName: nameFromDb, 
            token: tokenFromDb // KIRIM TOKEN KE MAINSCREEN
          )),
          (route) => false,
        );
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(backgroundColor: Colors.red, content: Text("Kode OTP Salah atau sudah Kadaluarsa!"))
        );
      }
    }

    return Scaffold(
      appBar: AppBar(
        title: const Text("Verifikasi Akun"),
        backgroundColor: spektaRed,
        foregroundColor: Colors.white,
      ),
      body: Padding(
        padding: const EdgeInsets.all(30),
        child: Column(
          children: [
            const SizedBox(height: 20),
            const Icon(Icons.verified_user, size: 80, color: spektaRed),
            const SizedBox(height: 30),
            Text(
              "Masukkan 6 digit kode yang dikirim ke WhatsApp:",
              textAlign: TextAlign.center,
              style: TextStyle(color: Colors.grey[700], fontSize: 16),
            ),
            Text(
              email,
              style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
            ),
            const SizedBox(height: 40),
            TextField(
              controller: otpCtrl,
              textAlign: TextAlign.center,
              keyboardType: TextInputType.number,
              maxLength: 6,
              style: const TextStyle(fontSize: 32, letterSpacing: 15, fontWeight: FontWeight.bold),
              decoration: InputDecoration(
                hintText: "000000",
                counterText: "", 
                enabledBorder: UnderlineInputBorder(borderSide: BorderSide(color: spektaRed.withOpacity(0.3))),
                focusedBorder: const UnderlineInputBorder(borderSide: BorderSide(color: spektaRed, width: 2)),
              ),
            ),
            const SizedBox(height: 50),
            ElevatedButton(
              style: ElevatedButton.styleFrom(
                backgroundColor: spektaRed,
                minimumSize: const Size(double.infinity, 55),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
              ),
              onPressed: handleVerify,
              child: const Text("VERIFIKASI SEKARANG", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
            ),
            const SizedBox(height: 20),
            TextButton(
              onPressed: () => Navigator.pop(context),
              child: const Text("Bukan email saya? Kembali", style: TextStyle(color: Colors.grey)),
            )
          ],
        ),
      ),
    );
  }
}
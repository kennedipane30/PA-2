import 'package:flutter/material.dart';
import '../services/auth_service.dart';
import 'login_page.dart'; 
import 'dart:convert';

class OtpPage extends StatefulWidget {
  final String name;
  final String email;

  const OtpPage({
    super.key, 
    required this.name, 
    required this.email,
  });

  @override
  State<OtpPage> createState() => _OtpPageState();
}

class _OtpPageState extends State<OtpPage> {
  final TextEditingController otpCtrl = TextEditingController();
  final Color spektaRed = const Color(0xFF990000);
  bool _isLoading = false;

  void handleVerify() async {
    if (otpCtrl.text.length < 6) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text("Masukkan 6 digit kode OTP"))
      );
      return;
    }

    setState(() => _isLoading = true);

    // Tampilkan Loading Dialog
    showDialog(
      context: context, 
      barrierDismissible: false, 
      builder: (_) => const Center(child: CircularProgressIndicator(color: Color(0xFF990000)))
    );

    try {
      // Mengirim email dan otp ke backend
      var resp = await AuthService.verifyRegistration(widget.email, otpCtrl.text);

      if (!mounted) return;
      Navigator.pop(context); // Tutup Loading Dialog

      if (resp.statusCode == 200) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            backgroundColor: Colors.green, 
            content: Text("Akun Berhasil Diaktifkan! Silakan Login.")
          )
        );

        // Redirect ke Login dan hapus history
        Navigator.pushAndRemoveUntil(
          context,
          MaterialPageRoute(builder: (context) => const LoginPage()),
          (route) => false,
        );
      } else {
        final errorBody = jsonDecode(resp.body);
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            backgroundColor: Colors.red, 
            content: Text(errorBody['message'] ?? "Kode OTP Salah atau Kadaluarsa")
          )
        );
      }
    } catch (e) {
      if (mounted) Navigator.pop(context);
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text("Koneksi Error!"), backgroundColor: Colors.red)
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
        title: const Text("Verifikasi Akun"),
        backgroundColor: Colors.white,
        foregroundColor: spektaRed,
        elevation: 0,
        centerTitle: true,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.symmetric(horizontal: 30),
        child: Column(
          children: [
            const SizedBox(height: 40),
            const Icon(Icons.mark_email_read_outlined, size: 80, color: Color(0xFF990000)),
            const SizedBox(height: 30),
            Text(
              "Halo ${widget.name},",
              style: const TextStyle(fontSize: 22, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 15),
            const Text(
              "Kami telah mengirimkan kode OTP ke email:",
              textAlign: TextAlign.center,
              style: TextStyle(color: Colors.grey, fontSize: 16),
            ),
            Text(
              widget.email,
              style: TextStyle(fontWeight: FontWeight.bold, color: spektaRed, fontSize: 16),
            ),
            
            const SizedBox(height: 50), // --- TULISAN SIMULASI SUDAH DIHAPUS ---
            
            TextField(
              controller: otpCtrl,
              textAlign: TextAlign.center,
              keyboardType: TextInputType.number,
              maxLength: 6,
              style: const TextStyle(fontSize: 32, letterSpacing: 20, fontWeight: FontWeight.bold, color: Color(0xFF990000)),
              decoration: InputDecoration(
                hintText: "000000",
                counterText: "",
                hintStyle: TextStyle(color: Colors.grey.shade300, letterSpacing: 20),
                enabledBorder: UnderlineInputBorder(borderSide: BorderSide(color: Colors.grey.shade300)),
                focusedBorder: UnderlineInputBorder(borderSide: BorderSide(color: spektaRed, width: 2)),
              ),
            ),
            
            const SizedBox(height: 60),
            
            _isLoading 
            ? CircularProgressIndicator(color: spektaRed)
            : ElevatedButton(
                style: ElevatedButton.styleFrom(
                  backgroundColor: spektaRed,
                  minimumSize: const Size(double.infinity, 60),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
                  elevation: 2,
                ),
                onPressed: handleVerify,
                child: const Text("VERIFIKASI & AKTIFKAN", 
                  style: TextStyle(color: Colors.white, fontSize: 16, fontWeight: FontWeight.bold)
                ),
              ),
            
            const SizedBox(height: 20),
            TextButton(
              onPressed: () {
                // Tambahkan fungsi kirim ulang jika diperlukan
              },
              child: Text("Tidak menerima email? Kirim ulang", style: TextStyle(color: spektaRed)),
            )
          ],
        ),
      ),
    );
  }
}
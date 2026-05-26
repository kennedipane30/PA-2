import 'package:flutter/material.dart';
import '../services/auth_service.dart';
import 'login_page.dart'; 
import 'dart:convert';
import 'dart:async'; // 1. TAMBAHKAN UNTUK TIMER

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

  // VARIABEL UNTUK TIMER
  Timer? _timer;
  int _start = 60;
  bool _isResendDisabled = true;

  @override
  void initState() {
    super.initState();
    startTimer(); // Mulai timer saat halaman dibuka
  }

  @override
  void dispose() {
    _timer?.cancel(); // Hapus timer saat pindah halaman
    super.dispose();
  }

  void startTimer() {
    _isResendDisabled = true;
    _start = 60;
    _timer = Timer.periodic(const Duration(seconds: 1), (timer) {
      if (_start == 0) {
        setState(() {
          timer.cancel();
          _isResendDisabled = false;
        });
      } else {
        setState(() => _start--);
      }
    });
  }

  // Fungsi untuk menyensor email (Contoh: st***@gmail.com)
  String maskEmail(String email) {
    final parts = email.split('@');
    if (parts[0].length <= 2) return email;
    return "${parts[0].substring(0, 2)}***@${parts[1]}";
  }

  void handleVerify() async {
    if (otpCtrl.text.length < 6) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text("Masukkan 6 digit kode OTP"))
      );
      return;
    }

    setState(() => _isLoading = true);

    showDialog(
      context: context, 
      barrierDismissible: false, 
      builder: (_) => const Center(child: CircularProgressIndicator(color: Color(0xFF990000)))
    );

    try {
      var resp = await AuthService.verifyRegistration(widget.email, otpCtrl.text);

      if (!mounted) return;
      Navigator.pop(context); 

      if (resp.statusCode == 200) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(backgroundColor: Colors.green, content: Text("Akun Aktif! Silakan Login."))
        );

        Navigator.pushAndRemoveUntil(
          context,
          MaterialPageRoute(builder: (context) => const LoginPage()),
          (route) => false,
        );
      } else {
        final errorBody = jsonDecode(resp.body);
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(backgroundColor: Colors.red, content: Text(errorBody['message'] ?? "OTP Salah"))
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
        backgroundColor: Colors.white,
        foregroundColor: spektaRed,
        elevation: 0,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.symmetric(horizontal: 30),
        child: Column(
          children: [
            const Icon(Icons.mark_email_read_rounded, size: 100, color: Color(0xFF990000)),
            const SizedBox(height: 30),
            Text(
              "Halo ${widget.name},",
              style: const TextStyle(fontSize: 24, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 10),
            const Text(
              "Masukkan 6 digit kode verifikasi yang dikirim ke:",
              textAlign: TextAlign.center,
              style: TextStyle(color: Colors.grey, fontSize: 14),
            ),
            Text(
              maskEmail(widget.email), // Email sudah disensor
              style: TextStyle(fontWeight: FontWeight.bold, color: spektaRed, fontSize: 16),
            ),
            
            const SizedBox(height: 40),
            
            TextField(
              controller: otpCtrl,
              textAlign: TextAlign.center,
              keyboardType: TextInputType.number,
              maxLength: 6,
              style: const TextStyle(fontSize: 35, letterSpacing: 15, fontWeight: FontWeight.bold, color: Color(0xFF990000)),
              decoration: InputDecoration(
                hintText: "------",
                counterText: "",
                hintStyle: TextStyle(color: Colors.grey.shade300),
                enabledBorder: UnderlineInputBorder(borderSide: BorderSide(color: Colors.grey.shade300)),
                focusedBorder: UnderlineInputBorder(borderSide: BorderSide(color: spektaRed, width: 3)),
              ),
            ),
            
            const SizedBox(height: 50),
            
            _isLoading 
            ? CircularProgressIndicator(color: spektaRed)
            : ElevatedButton(
                style: ElevatedButton.styleFrom(
                  backgroundColor: spektaRed,
                  minimumSize: const Size(double.infinity, 60),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
                ),
                onPressed: handleVerify,
                child: const Text("VERIFIKASI SEKARANG", 
                  style: TextStyle(color: Colors.white, fontSize: 16, fontWeight: FontWeight.bold)
                ),
              ),
            
            const SizedBox(height: 30),

            // TAMPILAN TOMBOL KIRIM ULANG DENGAN TIMER
            Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                const Text("Tidak menerima kode? ", style: TextStyle(color: Colors.grey, fontSize: 13)),
                _isResendDisabled 
                ? Text("Tunggu $_start detik", style: const TextStyle(color: Colors.black, fontWeight: FontWeight.bold, fontSize: 13))
                : TextButton(
                    onPressed: () {
                      // Panggil fungsi kirim ulang di sini
                      startTimer(); // Reset timer setelah klik
                    },
                    child: Text("Kirim Ulang", style: TextStyle(color: spektaRed, fontWeight: FontWeight.bold)),
                  ),
              ],
            )
          ],
        ),
      ),
    );
  }
}
import 'package:flutter/material.dart';
import '../services/auth_service.dart';
import 'main_screen.dart'; 
import 'dart:convert'; 

class OtpPage extends StatefulWidget {
  final String email;
  const OtpPage({super.key, required this.email});

  @override
  State<OtpPage> createState() => _OtpPageState();
}

class _OtpPageState extends State<OtpPage> {
  final TextEditingController otpCtrl = TextEditingController();
  final Color spektaRed = const Color(0xFF990000);
  String currentText = "";

  void handleVerify() async {
    if (otpCtrl.text.length < 6) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text("Masukkan 6 digit kode OTP lengkap"))
      );
      return;
    }

    showDialog(
      context: context, 
      barrierDismissible: false, 
      builder: (context) => const Center(child: CircularProgressIndicator(color: Color(0xFF990000)))
    );

    var resp = await AuthService.verifyOtp(widget.email, otpCtrl.text);
    
    if (!context.mounted) return;
    Navigator.pop(context); 

    if (resp.statusCode == 200) {
      final data = jsonDecode(resp.body);
      String nameFromDb = data['user']['name'] ?? "Siswa Spekta";
      String tokenFromDb = data['token']; 

      Navigator.pushAndRemoveUntil(
        context,
        MaterialPageRoute(builder: (_) => MainScreen(
          userName: nameFromDb, 
          token: tokenFromDb 
        )),
        (route) => false,
      );
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(backgroundColor: Colors.red, content: Text("Kode OTP Salah atau sudah Kadaluarsa!"))
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text("Verifikasi Akun"),
        backgroundColor: spektaRed,
        foregroundColor: Colors.white,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(30),
        child: Column(
          children: [
            const SizedBox(height: 20),
            const Icon(Icons.mark_chat_read_outlined, size: 80, color: Color(0xFF990000)),
            const SizedBox(height: 30),
            Text(
              "Masukkan 6 digit kode yang dikirim ke WhatsApp:",
              textAlign: TextAlign.center,
              style: TextStyle(color: Colors.grey[700], fontSize: 15),
            ),
            const SizedBox(height: 5),
            Text(
              widget.email,
              style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
            ),
            const SizedBox(height: 50),
            
            // --- CUSTOM OTP BOXES ---
            Stack(
              alignment: Alignment.center,
              children: [
                // Layer 1: Baris Kotak-Kotak Merah
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: List.generate(6, (index) {
                    return Container(
                      width: 45,
                      height: 55,
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(10),
                        border: Border.all(
                          color: currentText.length > index ? spektaRed : Colors.grey.shade300,
                          width: 2,
                        ),
                        boxShadow: currentText.length > index 
                          ? [BoxShadow(color: spektaRed.withOpacity(0.2), blurRadius: 5)] 
                          : [],
                      ),
                      child: Center(
                        child: Text(
                          currentText.length > index ? currentText[index] : "",
                          style: TextStyle(fontSize: 24, fontWeight: FontWeight.bold, color: spektaRed),
                        ),
                      ),
                    );
                  }),
                ),
                // Layer 2: Hidden TextField untuk Input
                Opacity(
                  opacity: 0,
                  child: TextField(
                    controller: otpCtrl,
                    onChanged: (value) {
                      setState(() {
                        currentText = value;
                      });
                    },
                    keyboardType: TextInputType.number,
                    maxLength: 6,
                    decoration: const InputDecoration(counterText: ""),
                  ),
                ),
              ],
            ),
            
            const SizedBox(height: 60),
            
            ElevatedButton(
              style: ElevatedButton.styleFrom(
                backgroundColor: spektaRed,
                minimumSize: const Size(double.infinity, 55),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
                elevation: 5,
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
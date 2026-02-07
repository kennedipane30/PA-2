import 'package:flutter/material.dart';
import '../services/auth_service.dart';
import 'home_page.dart';

class OtpPage extends StatelessWidget {
  final String email;
  const OtpPage({super.key, required this.email});

  @override Widget build(BuildContext context) {
    final otpCtrl = TextEditingController();
    return Scaffold(
      appBar: AppBar(title: const Text("Verifikasi WA")),
      body: Padding(padding: const EdgeInsets.all(30), child: Column(children: [
        Text("Masukkan kode yang dikirim ke WhatsApp $email"),
        TextField(controller: otpCtrl, textAlign: TextAlign.center, style: const TextStyle(fontSize: 30, letterSpacing: 10)),
        const SizedBox(height: 20),
        ElevatedButton(onPressed: () async {
          var resp = await AuthService.verifyOtp(email, otpCtrl.text);
          if (resp.statusCode == 200) Navigator.pushAndRemoveUntil(context, MaterialPageRoute(builder: (_) => const HomePage()), (r) => false);
        }, child: const Text("VERIFIKASI"))
      ])),
    );
  }
}
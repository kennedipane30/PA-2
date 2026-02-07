import 'package:flutter/material.dart';
import '../services/auth_service.dart';
import 'otp_page.dart';
import 'register_page.dart';

class LoginPage extends StatelessWidget {
  const LoginPage({super.key});
  @override Widget build(BuildContext context) {
    final emailCtrl = TextEditingController(), passCtrl = TextEditingController();
    return Scaffold(
      body: Padding(padding: const EdgeInsets.all(30), child: Column(mainAxisAlignment: MainAxisAlignment.center, children: [
        const Text("SPEKTA LOGIN", style: TextStyle(fontSize: 30, fontWeight: FontWeight.bold, color: Color(0xFF990000))),
        TextField(controller: emailCtrl, decoration: const InputDecoration(labelText: "Email")),
        TextField(controller: passCtrl, obscureText: true, decoration: const InputDecoration(labelText: "Password")),
        const SizedBox(height: 20),
        ElevatedButton(onPressed: () async {
          var resp = await AuthService.login(emailCtrl.text, passCtrl.text);
          if (resp.statusCode == 200) Navigator.push(context, MaterialPageRoute(builder: (_) => OtpPage(email: emailCtrl.text)));
        }, child: const Text("MASUK")),
        TextButton(onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const RegisterPage())), child: const Text("Daftar Akun Baru"))
      ])),
    );
  }
}
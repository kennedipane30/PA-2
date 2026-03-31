import 'package:flutter/material.dart';
import '../services/auth_service.dart';
import 'register_page.dart'; 
import 'main_screen.dart';   
import 'forgot_password_page.dart'; 
import 'dart:convert';
import 'package:shared_preferences/shared_preferences.dart';

class LoginPage extends StatefulWidget {
  const LoginPage({super.key});

  @override
  State<LoginPage> createState() => _LoginPageState();
}

class _LoginPageState extends State<LoginPage> {
  final TextEditingController nameCtrl = TextEditingController();
  final TextEditingController passCtrl = TextEditingController();
  final Color spektaRed = const Color(0xFF990000);

  // 1. State untuk fitur Ikon Mata (Password Visibility)
  bool _obscureText = true;
  bool _isLoading = false;

  void handleLogin() async {
    // Trim untuk menghapus spasi yang tidak sengaja terketik di awal/akhir
    String username = nameCtrl.text.trim();
    String password = passCtrl.text;

    if (username.isEmpty || password.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text("Username dan Password wajib diisi!"))
      );
      return;
    }

    setState(() => _isLoading = true);

    // Tampilkan Loading Dialog
    showDialog(
      context: context, 
      barrierDismissible: false, 
      builder: (context) => const Center(child: CircularProgressIndicator(color: Color(0xFF990000)))
    );

    try {
      // Pastikan AuthService.login sudah diubah untuk menerima username
      var resp = await AuthService.login(username, password);
      
      if (!mounted) return;
      Navigator.pop(context); // Tutup Loading

      if (resp.statusCode == 200) {
        final data = jsonDecode(resp.body);
        
        // Simpan token ke SharedPreferences agar tidak logout saat aplikasi ditutup
        SharedPreferences prefs = await SharedPreferences.getInstance();
        await prefs.setString('token', data['token']);

        if (!mounted) return;
        Navigator.pushAndRemoveUntil(
          context,
          MaterialPageRoute(builder: (_) => MainScreen(
            userName: data['user']['name'], 
            token: data['token'],
            userProfileData: data['user'], 
          )),
          (route) => false,
        );
      } else {
        final errorData = jsonDecode(resp.body);
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            backgroundColor: Colors.red, 
            content: Text(errorData['message'] ?? "Nama atau Password Salah!")
          )
        );
      }
    } catch (e) {
      if (!mounted) return;
      Navigator.pop(context);
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          backgroundColor: Colors.black, 
          content: Text("Koneksi Error: Pastikan server Laravel/Database menyala.")
        )
      );
    } finally {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.symmetric(horizontal: 35),
          child: Column(
            children: [
              const SizedBox(height: 80),
              const Text(
                "SPEKTA ACADEMY",
                style: TextStyle(
                  fontSize: 32,
                  fontWeight: FontWeight.bold,
                  color: Color(0xFF990000),
                  letterSpacing: 1.5,
                ),
                textAlign: TextAlign.center,
              ),
              const SizedBox(height: 80),
              
              // INPUT USERNAME / NAMA
              TextField(
                controller: nameCtrl,
                decoration: const InputDecoration(
                  labelText: "Username ",
                  prefixIcon: Icon(Icons.person_outline, color: Colors.grey),
                  enabledBorder: UnderlineInputBorder(borderSide: BorderSide(color: Colors.grey)),
                  focusedBorder: UnderlineInputBorder(borderSide: BorderSide(color: Color(0xFF990000), width: 2)),
                ),
              ),
              const SizedBox(height: 25),

              // INPUT PASSWORD DENGAN IKON MATA
              TextField(
                controller: passCtrl,
                obscureText: _obscureText, // Mengikuti state _obscureText
                decoration: InputDecoration(
                  labelText: "Password",
                  prefixIcon: const Icon(Icons.lock_outline, color: Colors.grey),
                  // FITUR IKON MATA
                  suffixIcon: IconButton(
                    icon: Icon(
                      _obscureText ? Icons.visibility_off_outlined : Icons.visibility_outlined,
                      color: Colors.grey,
                    ),
                    onPressed: () {
                      setState(() {
                        _obscureText = !_obscureText;
                      });
                    },
                  ),
                  enabledBorder: const UnderlineInputBorder(borderSide: BorderSide(color: Colors.grey)),
                  focusedBorder: const UnderlineInputBorder(borderSide: BorderSide(color: Color(0xFF990000), width: 2)),
                ),
              ),
              
              const SizedBox(height: 15),

              // TOMBOL LUPA PASSWORD
              Align(
                alignment: Alignment.centerRight,
                child: TextButton(
                  onPressed: () {
                    Navigator.push(
                      context,
                      MaterialPageRoute(builder: (context) => const ForgotPasswordPage()),
                    );
                  },
                  child: const Text(
                    "Lupa Password?",
                    style: TextStyle(color: Colors.grey, fontSize: 13, fontWeight: FontWeight.bold),
                  ),
                ),
              ),

              const SizedBox(height: 30),

              // TOMBOL MASUK
              ElevatedButton(
                style: ElevatedButton.styleFrom(
                  backgroundColor: spektaRed,
                  minimumSize: const Size(double.infinity, 60),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
                  elevation: 4,
                ),
                onPressed: _isLoading ? null : handleLogin,
                child: const Text(
                  "LOGIN",
                  style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16),
                ),
              ),
              
              const SizedBox(height: 30),

              // LINK REGISTRASI
              TextButton(
                onPressed: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(builder: (context) => const RegisterPage()),
                  );
                },
                child: RichText(
                  text: TextSpan(
                    text: "Belum punya akun? ",
                    style: const TextStyle(color: Colors.grey, fontSize: 13),
                    children: [
                      TextSpan(
                        text: "Klik di sini untuk registrasi",
                        style: TextStyle(color: spektaRed, fontWeight: FontWeight.bold),
                      ),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 20),
            ],
          ),
        ),
      ),
    );
  }
}
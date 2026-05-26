import 'package:flutter/material.dart';
import '../services/auth_service.dart';
import 'otp_page.dart'; // Pastikan file ini ada dan constructor-nya benar
import 'dart:convert';

class RegisterPage extends StatefulWidget {
  const RegisterPage({super.key});

  @override
  State<RegisterPage> createState() => _RegisterPageState();
}

class _RegisterPageState extends State<RegisterPage> {
  final _formKey = GlobalKey<FormState>();
  final Color spektaRed = const Color(0xFF990000);

  // Controllers
  final TextEditingController _nameCtrl = TextEditingController();
  final TextEditingController _emailCtrl = TextEditingController();
  final TextEditingController _waCtrl = TextEditingController();
  final TextEditingController _passCtrl = TextEditingController();
  final TextEditingController _confirmPassCtrl = TextEditingController();

  bool _obscurePassword = true;
  bool _obscureConfirm = true;

  String? _validateName(String? value) {
    if (value == null || value.isEmpty) return 'Nama wajib diisi';
    if (!RegExp(r'^[a-zA-Z\s]+$').hasMatch(value)) {
      return 'Nama hanya boleh berisi huruf!';
    }
    return null;
  }

  String? _validatePassword(String? value) {
    if (value == null || value.isEmpty) return 'Password wajib diisi';
    if (value.length < 8) return 'Minimal 8 karakter';
    
    bool hasUppercase = value.contains(RegExp(r'[A-Z]'));
    bool hasLowercase = value.contains(RegExp(r'[a-z]'));
    bool hasDigits = value.contains(RegExp(r'[0-9]'));
    bool hasSpecialCharacters = value.contains(RegExp(r'[!@#$%^&*(),.?":{}|<>]'));

    if (!hasUppercase || !hasLowercase || !hasDigits || !hasSpecialCharacters) {
      return 'Wajib: Kapital, Kecil, Angka, & Simbol';
    }
    return null;
  }

  void _handleRegister() async {
    if (_formKey.currentState!.validate()) {
      // Tampilkan Loading
      showDialog(
        context: context, 
        barrierDismissible: false, 
        builder: (_) => Center(child: CircularProgressIndicator(color: spektaRed))
      );

      Map<String, dynamic> data = {
        'name': _nameCtrl.text.trim(),
        'email': _emailCtrl.text.trim(),
        'nomor_wa': _waCtrl.text.trim(),
        'password': _passCtrl.text,
        'password_confirmation': _confirmPassCtrl.text,
      };

      try {
        var response = await AuthService.register(data);
        
        if (!mounted) return;
        Navigator.pop(context); // Tutup Loading

        if (response.statusCode == 201) {
          // NAVIGASI KE OTP PAGE DENGAN ARGUMEN YANG BENAR
          Navigator.push(
            context,
            MaterialPageRoute(
              builder: (context) => OtpPage(
                name: _nameCtrl.text.trim(), 
                email: _emailCtrl.text.trim(),
              ),
            ),
          );
        } else {
          final errorData = jsonDecode(response.body);
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              backgroundColor: Colors.red, 
              content: Text(errorData['message'] ?? "Registrasi Gagal")
            )
          );
        }
      } catch (e) {
        if (mounted) Navigator.pop(context);
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text("Koneksi Error! Periksa server backend Anda."), 
            backgroundColor: Colors.red
          )
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        elevation: 0,
        backgroundColor: Colors.white,
        foregroundColor: spektaRed,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.symmetric(horizontal: 25),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                "Buat Akun Baru",
                style: TextStyle(fontSize: 28, fontWeight: FontWeight.bold, color: spektaRed),
              ),
              const SizedBox(height: 8),
              const Text(
                "Bergabunglah dengan Spekta Academy dan mulai belajarmu hari ini!",
                style: TextStyle(fontSize: 16, color: Colors.grey),
              ),
              const SizedBox(height: 30),

              _buildInput(_nameCtrl, "Nama Lengkap", Icons.person_outline, _validateName),
              _buildInput(_emailCtrl, "Email", Icons.email_outlined, (v) => (v != null && v.contains('@')) ? null : "Email tidak valid"),
              _buildInput(_waCtrl, "WhatsApp (Aktif)", Icons.phone_android_outlined, (v) => (v != null && v.length < 10) ? "Nomor tidak valid" : null),
              
              TextFormField(
                controller: _passCtrl,
                obscureText: _obscurePassword,
                decoration: InputDecoration(
                  labelText: "Password",
                  prefixIcon: const Icon(Icons.lock_open_rounded),
                  suffixIcon: IconButton(
                    icon: Icon(_obscurePassword ? Icons.visibility_off : Icons.visibility, color: Colors.grey),
                    onPressed: () => setState(() => _obscurePassword = !_obscurePassword),
                  ),
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(15)),
                ),
                validator: _validatePassword,
              ),
              const SizedBox(height: 15),

              TextFormField(
                controller: _confirmPassCtrl,
                obscureText: _obscureConfirm,
                decoration: InputDecoration(
                  labelText: "Konfirmasi Password",
                  prefixIcon: const Icon(Icons.lock_rounded),
                  suffixIcon: IconButton(
                    icon: Icon(_obscureConfirm ? Icons.visibility_off : Icons.visibility, color: Colors.grey),
                    onPressed: () => setState(() => _obscureConfirm = !_obscureConfirm),
                  ),
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(15)),
                ),
                validator: (v) => v != _passCtrl.text ? 'Password tidak cocok' : null,
              ),

              const SizedBox(height: 40),

              ElevatedButton(
                style: ElevatedButton.styleFrom(
                  backgroundColor: spektaRed,
                  minimumSize: const Size(double.infinity, 60),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15))
                ),
                onPressed: _handleRegister,
                child: const Text(
                  "DAFTAR SEKARANG", 
                  style: TextStyle(color: Colors.white, fontSize: 16, fontWeight: FontWeight.bold)
                ),
              ),
              const SizedBox(height: 30),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildInput(TextEditingController ctrl, String label, IconData icon, String? Function(String?)? validator) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 20),
      child: TextFormField(
        controller: ctrl,
        decoration: InputDecoration(
          labelText: label,
          prefixIcon: Icon(icon),
          border: OutlineInputBorder(borderRadius: BorderRadius.circular(15)),
        ),
        validator: validator,
      ),
    );
  }
}
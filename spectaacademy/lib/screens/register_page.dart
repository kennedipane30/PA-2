import 'package:flutter/material.dart';
import '../services/auth_service.dart';
import 'dart:convert'; // WAJIB ADA untuk membaca pesan error dari Laravel

class RegisterPage extends StatefulWidget {
  const RegisterPage({super.key});
  @override State<RegisterPage> createState() => _RegisterPageState();
}

class _RegisterPageState extends State<RegisterPage> {
  final _formKey = GlobalKey<FormState>();
  final Color spektaRed = const Color(0xFF990000);

  // Controllers
  final TextEditingController _name = TextEditingController();
  final TextEditingController _email = TextEditingController();
  final TextEditingController _tgl = TextEditingController();
  final TextEditingController _alamat = TextEditingController();
  final TextEditingController _wa = TextEditingController();
  final TextEditingController _waO = TextEditingController();
  final TextEditingController _pass = TextEditingController();
  final TextEditingController _conf = TextEditingController();

  // VALIDASI PASSWORD (Syarat Keamanan Perangkat Lunak)
  String? _validatePassword(String? value) {
    if (value == null || value.isEmpty) return 'Password tidak boleh kosong';
    if (value.length < 8) return 'Minimal 8 karakter';
    
    bool hasUppercase = value.contains(RegExp(r'[A-Z]'));
    bool hasDigits = value.contains(RegExp(r'[0-9]'));
    bool hasSpecialCharacters = value.contains(RegExp(r'[!@#$%^&*(),.?":{}|<>]'));

    if (!hasUppercase) return 'Wajib ada 1 Huruf KAPITAL';
    if (!hasDigits) return 'Wajib ada 1 ANGKA';
    if (!hasSpecialCharacters) return 'Wajib ada 1 SIMBOL (!@# dll)';
    
    return null;
  }

  // FUNGSI HANDLE REGISTER DENGAN PENANGANAN ERROR LENGKAP
  void _handleRegister() async {
    if (_formKey.currentState!.validate()) {
      // 1. Tampilkan Loading (Agar User tidak klik berkali-kali)
      showDialog(
        context: context, 
        barrierDismissible: false, 
        builder: (context) => const Center(child: CircularProgressIndicator(color: Color(0xFF990000)))
      );

      try {
        var response = await AuthService.register({
          'name': _name.text,
          'email': _email.text,
          'tanggal_lahir': _tgl.text,
          'alamat': _alamat.text,
          'nomor_wa': _wa.text,
          'nomor_wa_ortu': _waO.text,
          'password': _pass.text,
          'password_confirmation': _conf.text,
        });

        Navigator.pop(context); // Tutup Loading

        if (response.statusCode == 201) {
          // BERHASIL
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(backgroundColor: Colors.green, content: Text("Registrasi Berhasil! Silakan Login."))
          );
          Navigator.pop(context); 
        } else {
          // GAGAL - Ambil pesan error dari Laravel
          final errorBody = jsonDecode(response.body);
          String errorMessage = "Gagal Simpan";

          if (errorBody['errors'] != null) {
            // Ambil pesan error validasi pertama
            errorMessage = errorBody['errors'].values.first[0];
          } else {
            errorMessage = errorBody['message'] ?? "Terjadi kesalahan server";
          }

          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(backgroundColor: Colors.red, content: Text("Gagal: $errorMessage"))
          );
        }
      } catch (e) {
        Navigator.pop(context); // Tutup Loading
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(backgroundColor: Colors.black, content: Text("Koneksi Error: Periksa apakah Server sudah menyala."))
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text("Registrasi Siswa Spekta"), backgroundColor: spektaRed, foregroundColor: Colors.white),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Form(
          key: _formKey,
          child: Column(
            children: [
              _buildField(_name, "Nama Lengkap", Icons.person),
              _buildField(_email, "Gmail / Email", Icons.email),
              _buildField(_tgl, "Tgl Lahir (Contoh: 2005-10-30)", Icons.calendar_today),
              _buildField(_alamat, "Alamat Lengkap", Icons.map),
              _buildField(_wa, "No HP Siswa (Contoh: 0822xxx)", Icons.phone_android),
              _buildField(_waO, "No HP Orang Tua", Icons.family_restroom),
              
              TextFormField(
                controller: _pass,
                obscureText: true,
                decoration: const InputDecoration(labelText: "Password", icon: Icon(Icons.lock_outline)),
                validator: _validatePassword,
              ),
              
              TextFormField(
                controller: _conf,
                obscureText: true,
                decoration: const InputDecoration(labelText: "Konfirmasi Password", icon: Icon(Icons.lock)),
                validator: (v) => v != _pass.text ? 'Password tidak cocok' : null,
              ),

              const SizedBox(height: 30),
              ElevatedButton(
                style: ElevatedButton.styleFrom(
                  backgroundColor: spektaRed, 
                  minimumSize: const Size(double.infinity, 55),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10))
                ),
                onPressed: _handleRegister,
                child: const Text("DAFTAR SEKARANG", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
              ),
              const SizedBox(height: 20),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildField(TextEditingController ctrl, String label, IconData icon) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 10),
      child: TextFormField(
        controller: ctrl,
        decoration: InputDecoration(labelText: label, icon: Icon(icon)),
        validator: (v) => v!.isEmpty ? '$label wajib diisi' : null,
      ),
    );
  }
}
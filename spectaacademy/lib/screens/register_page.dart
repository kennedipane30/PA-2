import 'package:flutter/material.dart';
import '../services/auth_service.dart';

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

  // VALIDASI PASSWORD (Mata Kuliah Keamanan)
  String? _validatePassword(String? value) {
    if (value == null || value.isEmpty) return 'Password tidak boleh kosong';
    if (value.length < 8) return 'Minimal 8 karakter';
    
    // Pattern: Kapital, Angka, Simbol
    bool hasUppercase = value.contains(RegExp(r'[A-Z]'));
    bool hasDigits = value.contains(RegExp(r'[0-9]'));
    bool hasSpecialCharacters = value.contains(RegExp(r'[!@#$%^&*(),.?":{}|<>]'));

    if (!hasUppercase) return 'Wajib ada 1 Huruf KAPITAL';
    if (!hasDigits) return 'Wajib ada 1 ANGKA';
    if (!hasSpecialCharacters) return 'Wajib ada 1 SIMBOL (!@# dll)';
    
    return null;
  }

  void _handleRegister() async {
    if (_formKey.currentState!.validate()) {
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

      if (response.statusCode == 201) {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(backgroundColor: Colors.green, content: Text("Registrasi Berhasil! Silakan Login.")));
        Navigator.pop(context); // Kembali ke halaman Login
      } else {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(backgroundColor: Colors.red, content: Text("Gagal Simpan. Cek data Anda.")));
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
              _buildField(_email, "Gmail", Icons.email),
              _buildField(_tgl, "Tgl Lahir (YYYY-MM-DD)", Icons.calendar_today),
              _buildField(_alamat, "Alamat Lengkap", Icons.map),
              _buildField(_wa, "No HP Siswa", Icons.phone_android),
              _buildField(_waO, "No HP Orang Tua", Icons.family_restroom),
              
              // Input Password dengan validasi rumit
              TextFormField(
                controller: _pass,
                obscureText: true,
                decoration: const InputDecoration(labelText: "Password", icon: Icon(Icons.lock_outline)),
                validator: _validatePassword,
              ),
              
              // Konfirmasi Password
              TextFormField(
                controller: _conf,
                obscureText: true,
                decoration: const InputDecoration(labelText: "Konfirmasi Password", icon: Icon(Icons.lock)),
                validator: (v) => v != _pass.text ? 'Password tidak cocok' : null,
              ),

              const SizedBox(height: 30),
              ElevatedButton(
                style: ElevatedButton.styleFrom(backgroundColor: spektaRed, minimumSize: const Size(double.infinity, 50)),
                onPressed: _handleRegister,
                child: const Text("DAFTAR SEKARANG", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildField(TextEditingController ctrl, String label, IconData icon) {
    return TextFormField(
      controller: ctrl,
      decoration: InputDecoration(labelText: label, icon: Icon(icon)),
      validator: (v) => v!.isEmpty ? '$label wajib diisi' : null,
    );
  }
}
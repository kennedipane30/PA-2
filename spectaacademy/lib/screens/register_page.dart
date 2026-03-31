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
  final TextEditingController _pass = TextEditingController();
  final TextEditingController _conf = TextEditingController();
  final TextEditingController _alamat = TextEditingController();
  final TextEditingController _namaIbu = TextEditingController();
  final TextEditingController _hpSiswa = TextEditingController();
  final TextEditingController _hpOrtu = TextEditingController();
  final TextEditingController _hpOrtu2 = TextEditingController();
  
  bool _isLoading = false;
  bool _obscurePassword = true;
  bool _obscureConfirmPassword = true;

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
      setState(() => _isLoading = true);
      
      var response = await AuthService.register({
        'name': _name.text,
        'email': _email.text,
        'password': _pass.text,
        'password_confirmation': _conf.text,
        'alamat': _alamat.text,
        'nama_ibu': _namaIbu.text,
        'hp_siswa': _hpSiswa.text,
        'hp_ortu': _hpOrtu.text,
        'hp_ortu_2': _hpOrtu2.text.isEmpty ? null : _hpOrtu2.text,
      });

      if (!mounted) return;
      
      setState(() => _isLoading = false);

      if (response.statusCode == 201) {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(backgroundColor: Colors.green, content: Text("Registrasi Berhasil! Silakan Login.")));
        Navigator.pop(context);
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
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              _buildLabeledField(_name, "Nama lengkap", false),
              const SizedBox(height: 16),
              
              _buildLabeledField(_email, "Alamat email", false, keyboardType: TextInputType.emailAddress),
              const SizedBox(height: 16),
              
              _buildPasswordField(_pass, "Kata sandi", _obscurePassword, 
                () => setState(() => _obscurePassword = !_obscurePassword),
                validator: _validatePassword),
              const SizedBox(height: 16),
              
              _buildPasswordField(_conf, "Konfirmasi kata sandi", _obscureConfirmPassword,
                () => setState(() => _obscureConfirmPassword = !_obscureConfirmPassword),
                validator: (v) => v != _pass.text ? 'Password tidak cocok' : null),
              const SizedBox(height: 16),
              
              _buildLabeledField(_alamat, "Alamat", false),
              const SizedBox(height: 16),
              
              _buildLabeledField(_namaIbu, "Nama Ibu", false),
              const SizedBox(height: 16),
              
              _buildLabeledField(_hpSiswa, "No. HP Siswa", false, keyboardType: TextInputType.phone),
              const SizedBox(height: 16),
              
              _buildLabeledField(_hpOrtu, "No. HP Orang Tua", false, keyboardType: TextInputType.phone),
              const SizedBox(height: 16),
              
              _buildLabeledField(_hpOrtu2, "No. HP Orang Tua 2 (Optional)", false, 
                keyboardType: TextInputType.phone, isOptional: true),
              const SizedBox(height: 30),
              
              ElevatedButton(
                style: ElevatedButton.styleFrom(
                  backgroundColor: spektaRed, 
                  minimumSize: const Size(double.infinity, 50),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                ),
                onPressed: _isLoading ? null : _handleRegister,
                child: _isLoading 
                  ? const SizedBox(
                      height: 20,
                      width: 20,
                      child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2),
                    )
                  : const Text("DAFTAR SEKARANG", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildPasswordField(
    TextEditingController ctrl,
    String label,
    bool obscureText,
    VoidCallback onToggle, {
    String? Function(String?)? validator,
  }) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          label,
          style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w500, color: Colors.black87),
        ),
        const SizedBox(height: 8),
        TextFormField(
          controller: ctrl,
          obscureText: obscureText,
          decoration: InputDecoration(
            hintText: 'Masukkan $label',
            border: OutlineInputBorder(borderRadius: BorderRadius.circular(8)),
            contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
            filled: true,
            fillColor: Colors.grey[50],
            suffixIcon: IconButton(
              icon: Icon(
                obscureText ? Icons.visibility_off : Icons.visibility,
                color: Colors.grey[600],
              ),
              onPressed: onToggle,
            ),
          ),
          validator: validator,
        ),
      ],
    );
  }

  Widget _buildLabeledField(
    TextEditingController ctrl, 
    String label, 
    bool isPassword, {
    String? Function(String?)? validator,
    TextInputType? keyboardType,
    bool isOptional = false,
  }) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          label,
          style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w500, color: Colors.black87),
        ),
        const SizedBox(height: 8),
        TextFormField(
          controller: ctrl,
          obscureText: isPassword,
          keyboardType: keyboardType,
          decoration: InputDecoration(
            hintText: 'Masukkan $label',
            border: OutlineInputBorder(borderRadius: BorderRadius.circular(8)),
            contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
            filled: true,
            fillColor: Colors.grey[50],
          ),
          validator: validator ?? (v) => !isOptional && (v == null || v.isEmpty) ? '$label wajib diisi' : null,
        ),
      ],
    );
  }
}
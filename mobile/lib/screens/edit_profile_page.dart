import 'package:flutter/material.dart';
import '../services/auth_service.dart';
import 'package:intl/intl.dart';
import 'dart:convert';
  
class EditProfilePage extends StatefulWidget {
  final Map userData;
  final String token;
  const EditProfilePage({super.key, required this.userData, required this.token});

  @override
  State<EditProfilePage> createState() => _EditProfilePageState();
}

class _EditProfilePageState extends State<EditProfilePage> {
  final _parentCtrl = TextEditingController();
  final _alamatCtrl = TextEditingController();
  final _waOrtuCtrl = TextEditingController();
  final _nisnCtrl = TextEditingController();
  final _dobCtrl = TextEditingController();
  
  final Color spektaRed = const Color(0xFF990000);
  final Color spektaBg = const Color(0xFFF8F9FA);

  @override
  void initState() {
    super.initState();
    if (widget.userData['student'] != null) {
      var s = widget.userData['student'];
      // Jika data adalah "-", kita kosongkan agar TextField menampilkan hint
      _parentCtrl.text = s['parent_name'] == "-" ? "" : (s['parent_name'] ?? "");
      _alamatCtrl.text = s['school'] == "-" ? "" : (s['school'] ?? "");
      _waOrtuCtrl.text = s['wa_ortu'] == "-" ? "" : (s['wa_ortu'] ?? "");
      _nisnCtrl.text = s['nisn'] == "-" ? "" : (s['nisn'] ?? "");
      _dobCtrl.text = s['dob'] == "-" ? "" : (s['dob'] ?? "");
    }
  }

  Future<void> _selectDate() async {
    DateTime? picked = await showDatePicker(
      context: context,
      initialDate: DateTime(2007),
      firstDate: DateTime(1990),
      lastDate: DateTime.now(),
      builder: (context, child) {
        return Theme(
          data: Theme.of(context).copyWith(
            colorScheme: ColorScheme.light(primary: spektaRed),
          ),
          child: child!,
        );
      },
    );
    if (picked != null) {
      setState(() => _dobCtrl.text = DateFormat('yyyy-MM-dd').format(picked));
    }
  }

  void _handleSave() async {
    if (_parentCtrl.text.isEmpty || _alamatCtrl.text.isEmpty || _nisnCtrl.text.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text("Mohon lengkapi semua kolom yang tersedia")),
      );
      return;
    }

    showDialog(
      context: context, 
      barrierDismissible: false,
      builder: (_) => Center(child: CircularProgressIndicator(color: spektaRed))
    );

    var resp = await AuthService.updateProfile({
      'parent_name': _parentCtrl.text,
      'alamat': _alamatCtrl.text,
      'wa_ortu': _waOrtuCtrl.text,
      'nisn': _nisnCtrl.text,
      'dob': _dobCtrl.text,
    }, widget.token);

    if (!mounted) return;
    Navigator.pop(context);

    if (resp.statusCode == 200) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(backgroundColor: Colors.green, content: Text("Profil berhasil diperbarui!")),
      );
      Navigator.pop(context, true);
    } else {
      final err = jsonDecode(resp.body);
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(backgroundColor: Colors.red, content: Text(err['message'] ?? "Terjadi kesalahan")),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: const Text("Lengkapi Profil", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 18)),
        backgroundColor: Colors.white,
        foregroundColor: Colors.black,
        elevation: 0,
        centerTitle: true,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.symmetric(horizontal: 24),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text(
              "Tinggal selangkah lagi!",
              style: TextStyle(fontSize: 22, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 8),
            Text(
              "Lengkapi data di bawah untuk mendaftar kelas.",
              style: TextStyle(color: Colors.grey[600], fontSize: 14),
            ),
            const SizedBox(height: 24),
            
            // CARD INFO AKUN (MODERN LOOK)
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: spektaBg,
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: Colors.grey.shade200),
              ),
              child: Column(
                children: [
                  _buildInfoRow(Icons.email_outlined, "Gmail", widget.userData['email']),
                  const Padding(
                    padding: EdgeInsets.symmetric(vertical: 12),
                    child: Divider(height: 1),
                  ),
                  _buildInfoRow(Icons.phone_android, "WhatsApp", widget.userData['phone']),
                ],
              ),
            ),
            const SizedBox(height: 32),
            
            // FORM INPUT
            _buildModernInput(_nisnCtrl, "NISN Siswa", Icons.numbers_rounded, TextInputType.number),
            _buildModernInput(_parentCtrl, "Nama Orang Tua", Icons.person_outline_rounded, TextInputType.name),
            _buildModernInput(_alamatCtrl, "Alamat Lengkap / Asal Sekolah", Icons.location_on_outlined, TextInputType.streetAddress),
            _buildModernInput(_waOrtuCtrl, "WhatsApp Orang Tua", Icons.phone_iphone_rounded, TextInputType.phone),
            
            // TANGGAL LAHIR
            const Text("Tanggal Lahir", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 14)),
            const SizedBox(height: 8),
            TextField(
              controller: _dobCtrl,
              readOnly: true,
              onTap: _selectDate,
              decoration: InputDecoration(
                hintText: "Pilih Tanggal",
                prefixIcon: Icon(Icons.calendar_today_rounded, color: spektaRed, size: 20),
                filled: true,
                fillColor: spektaBg,
                contentPadding: const EdgeInsets.symmetric(vertical: 16),
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide.none),
              ),
            ),
            
            const SizedBox(height: 40),
            
            // TOMBOL SIMPAN
            ElevatedButton(
              style: ElevatedButton.styleFrom(
                backgroundColor: spektaRed,
                minimumSize: const Size(double.infinity, 56),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                elevation: 4,
                shadowColor: spektaRed.withOpacity(0.4),
              ),
              onPressed: _handleSave,
              child: const Text("SIMPAN PERUBAHAN", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)),
            ),
            const SizedBox(height: 30),
          ],
        ),
      ),
    );
  }

  Widget _buildInfoRow(IconData icon, String label, String? value) {
    return Row(
      children: [
        Icon(icon, size: 20, color: Colors.grey[600]),
        const SizedBox(width: 12),
        Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(label, style: TextStyle(color: Colors.grey[500], fontSize: 12)),
            Text(value ?? "-", style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14)),
          ],
        ),
      ],
    );
  }

  Widget _buildModernInput(TextEditingController ctrl, String label, IconData icon, TextInputType type) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(label, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14)),
        const SizedBox(height: 8),
        Padding(
          padding: const EdgeInsets.only(bottom: 20),
          child: TextField(
            controller: ctrl,
            keyboardType: type,
            decoration: InputDecoration(
              hintText: "Masukkan $label",
              hintStyle: TextStyle(color: Colors.grey[400], fontSize: 14),
              prefixIcon: Icon(icon, color: spektaRed, size: 20),
              filled: true,
              fillColor: spektaBg,
              contentPadding: const EdgeInsets.symmetric(vertical: 16),
              border: OutlineInputBorder(
                borderRadius: BorderRadius.circular(12),
                borderSide: BorderSide.none,
              ),
              focusedBorder: OutlineInputBorder(
                borderRadius: BorderRadius.circular(12),
                borderSide: BorderSide(color: spektaRed, width: 1.5),
              ),
            ),
          ),
        ),
      ],
    );
  }
}
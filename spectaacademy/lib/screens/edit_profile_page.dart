import 'package:flutter/material.dart';
import '../services/auth_service.dart';
import 'dart:convert'; // WAJIB ADA untuk memproses respon JSON

class EditProfilePage extends StatefulWidget {
  final Map userData; 
  final String token;
  const EditProfilePage({super.key, required this.userData, required this.token});

  @override State<EditProfilePage> createState() => _EditProfilePageState();
}

class _EditProfilePageState extends State<EditProfilePage> {
  final _parentCtrl = TextEditingController();
  final _alamatCtrl = TextEditingController();
  final _waOrtuCtrl = TextEditingController();
  final Color spektaRed = const Color(0xFF990000);

  @override void initState() {
    super.initState();
    // Isi otomatis jika data sudah ada di database (Sesuai ERD Student)
    if (widget.userData['student'] != null) {
      _parentCtrl.text = widget.userData['student']['parent_name'] ?? "";
      _alamatCtrl.text = widget.userData['student']['school'] ?? "";
      _waOrtuCtrl.text = widget.userData['student']['wa_ortu'] ?? "";
    }
  }

 void _saveProfile() async {
  if (_parentCtrl.text.isEmpty || _alamatCtrl.text.isEmpty || _waOrtuCtrl.text.isEmpty) {
    ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text("Isi semua data!")));
    return;
  }

  showDialog(context: context, builder: (_) => const Center(child: CircularProgressIndicator(color: Color(0xFF990000))));

  var resp = await AuthService.updateProfile({
    'parent_name': _parentCtrl.text,
    'alamat': _alamatCtrl.text,
    'wa_ortu': _waOrtuCtrl.text,
  }, widget.token);

  if (!mounted) return;
  Navigator.pop(context); // Tutup Loading

  if (resp.statusCode == 200) {
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(backgroundColor: Colors.green, content: Text("Data diri anda berhasil dilengkapi"))
    );
    Future.delayed(const Duration(seconds: 1), () => Navigator.pop(context, true));
  }
}
  
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: const Text("Lengkapi Data Diri", style: TextStyle(fontWeight: FontWeight.bold)),
        backgroundColor: spektaRed,
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(25),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // INFO GMAIL & NO WA (Tampil Otomatis & Read Only)
            const Text("Data Akun Terdaftar", style: TextStyle(fontWeight: FontWeight.bold, color: Colors.grey)),
            const SizedBox(height: 10),
            Container(
              padding: const EdgeInsets.all(10),
              decoration: BoxDecoration(color: Colors.grey[100], borderRadius: BorderRadius.circular(15)),
              child: Column(
                children: [
                  ListTile(
                    leading: const Icon(Icons.email_outlined, color: Colors.grey), 
                    title: const Text("Gmail", style: TextStyle(fontSize: 12, color: Colors.grey)),
                    subtitle: Text(widget.userData['email'] ?? "-", style: const TextStyle(fontWeight: FontWeight.bold, color: Colors.black87)),
                  ),
                  const Divider(height: 1),
                  ListTile(
                    leading: const Icon(Icons.phone_android_outlined, color: Colors.grey), 
                    title: const Text("Nomor WhatsApp", style: TextStyle(fontSize: 12, color: Colors.grey)),
                    subtitle: Text(widget.userData['phone'] ?? "-", style: const TextStyle(fontWeight: FontWeight.bold, color: Colors.black87)),
                  ),
                ],
              ),
            ),
            
            const SizedBox(height: 30),
            const Text("Data Orang Tua & Alamat", style: TextStyle(fontWeight: FontWeight.bold, color: Colors.grey)),
            const SizedBox(height: 15),
            
            // FORM INPUT
            TextField(
              controller: _parentCtrl, 
              decoration: InputDecoration(
                labelText: "Nama Orang Tua", 
                prefixIcon: const Icon(Icons.person_outline, color: Color(0xFF990000)),
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(15))
              )
            ),
            const SizedBox(height: 15),
            TextField(
              controller: _alamatCtrl, 
              decoration: InputDecoration(
                labelText: "Alamat Lengkap", 
                prefixIcon: const Icon(Icons.location_on_outlined, color: Color(0xFF990000)),
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(15))
              )
            ),
            const SizedBox(height: 15),
            TextField(
              controller: _waOrtuCtrl, 
              keyboardType: TextInputType.phone, 
              decoration: InputDecoration(
                labelText: "WhatsApp Orang Tua", 
                prefixIcon: const Icon(Icons.phone_android, color: Color(0xFF990000)),
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(15))
              )
            ),
            
            const SizedBox(height: 50),
            
            // TOMBOL SIMPAN
            ElevatedButton(
              style: ElevatedButton.styleFrom(
                backgroundColor: spektaRed,
                minimumSize: const Size(double.infinity, 55),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
                elevation: 5,
              ),
              onPressed: _saveProfile,
              child: const Text("SIMPAN DATA", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)),
            )
          ],
        ),
      ),
    );
  }
}
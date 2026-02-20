import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart'; // Wajib ada di pubspec.yaml
import 'dart:io';
import 'dart:convert';
import 'package:http/http.dart' as http;
import '../services/auth_service.dart';

class ClassDetailPage extends StatefulWidget {
  final int classId;
  final String className;
  final String token;
  final Map userData; // Untuk data pengecekan profil

  const ClassDetailPage({
    super.key,
    required this.classId,
    required this.className,
    required this.token,
    required this.userData,
  });

  @override
  State<ClassDetailPage> createState() => _ClassDetailPageState();
}

class _ClassDetailPageState extends State<ClassDetailPage> {
  String status = "none"; // none, pending, aktif
  bool isLoading = true;
  final Color spektaRed = const Color(0xFF990000);

  @override
  void initState() {
    super.initState();
    _fetchStatus();
  }

  // 1. CEK STATUS PENDAFTARAN (Mata Kuliah: Aplikasi Terdistribusi)
  Future<void> _fetchStatus() async {
    try {
      var resp = await AuthService.checkClassStatus(widget.classId, widget.token);
      if (resp.statusCode == 200) {
        setState(() {
          status = jsonDecode(resp.body)['status'];
          isLoading = false;
        });
      }
    } catch (e) {
      print("Error fetch status: $e");
    }
  }

  // 2. LOGIKA DAFTAR & UPLOAD (Mata Kuliah: Keamanan & Cloud)
  void _handleDaftar() async {
    // A. Pilih Gambar Bukti Transfer
    final ImagePicker picker = ImagePicker();
    final XFile? image = await picker.pickImage(source: ImageSource.gallery);

    if (image == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text("Pilih foto bukti transfer untuk mendaftar!"))
      );
      return;
    }

    // B. Tampilkan Loading
    showDialog(
      context: context, 
      barrierDismissible: false, 
      builder: (_) => const Center(child: CircularProgressIndicator(color: Color(0xFF990000)))
    );

    try {
      // C. Kirim Data Multipart ke Laravel
      var streamedResponse = await AuthService.joinClass(
        widget.classId, 
        image.path, 
        widget.token
      );

      // D. Konversi Stream ke Response Biasa
      var response = await http.Response.fromStream(streamedResponse);
      final responseData = jsonDecode(response.body);

      if (!mounted) return;
      Navigator.pop(context); // Tutup Loading

      if (response.statusCode == 200) {
        // SUKSES
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(backgroundColor: Colors.green, content: Text("Bukti Terkirim! Menunggu Verifikasi Admin."))
        );
        _fetchStatus(); // Update UI jadi 'pending'
      } 
      else if (response.statusCode == 403) {
        // TERBLOKIR: PROFIL BELUM LENGKAP (Gatekeeper Logic)
        showDialog(
          context: context,
          builder: (context) => AlertDialog(
            title: const Text("Profil Belum Lengkap", style: TextStyle(fontWeight: FontWeight.bold, color: Color(0xFF990000))),
            content: Text(responseData['message'] ?? "Lengkapi data diri di menu Akun."),
            actions: [
              TextButton(
                onPressed: () => Navigator.pop(context), 
                child: const Text("OKE, SAYA LENGKAPI", style: TextStyle(fontWeight: FontWeight.bold))
              )
            ],
          ),
        );
      } 
      else {
        // ERROR LAINNYA
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(backgroundColor: Colors.red, content: Text(responseData['message'] ?? "Gagal mendaftar"))
        );
      }
    } catch (e) {
      if (!mounted) return;
      Navigator.pop(context);
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text("Koneksi Error!")));
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(widget.className, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 18)),
        backgroundColor: spektaRed,
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      body: isLoading 
        ? Center(child: CircularProgressIndicator(color: spektaRed)) 
        : Column(
            children: [
              // Banner Feedback Status
              _buildStatusBanner(),
              
              // List Materi (Locked/Unlocked)
              Expanded(
                child: ListView.builder(
                  padding: const EdgeInsets.all(15),
                  itemCount: 5, // Contoh ada 5 materi
                  itemBuilder: (context, index) {
                    bool isLocked = status != 'aktif';
                    return Card(
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
                      elevation: 2,
                      child: ListTile(
                        leading: Icon(
                          isLocked ? Icons.lock_outline : Icons.play_circle_fill, 
                          color: isLocked ? Colors.grey : Colors.green,
                          size: 30,
                        ),
                        title: Text("Materi Video Pertemuan ${index + 1}", style: const TextStyle(fontWeight: FontWeight.bold)),
                        subtitle: Text(isLocked ? "Daftar untuk membuka" : "Klik untuk menonton"),
                        trailing: const Icon(Icons.chevron_right),
                        onTap: isLocked ? () {
                          ScaffoldMessenger.of(context).showSnackBar(
                            const SnackBar(content: Text("Materi Terkunci! Silakan daftar kelas."))
                          );
                        } : () {
                          // Aksi buka video
                        },
                      ),
                    );
                  },
                ),
              )
            ],
          ),
    );
  }

  Widget _buildStatusBanner() {
    if (status == 'none') {
      return Container(
        width: double.infinity,
        padding: const EdgeInsets.all(20),
        color: Colors.red[50],
        child: Column(
          children: [
            const Text("Anda belum memiliki akses ke materi ini.", style: TextStyle(fontWeight: FontWeight.bold)),
            const SizedBox(height: 10),
            ElevatedButton.icon(
              onPressed: _handleDaftar, 
              icon: const Icon(Icons.assignment_ind, color: Colors.white),
              label: const Text("DAFTAR & KIRIM BUKTI BAYAR", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
              style: ElevatedButton.styleFrom(backgroundColor: spektaRed, minimumSize: const Size(double.infinity, 45)),
            )
          ],
        ),
      );
    } else if (status == 'pending') {
      return Container(
        width: double.infinity,
        padding: const EdgeInsets.all(15),
        color: Colors.orange[50],
        child: const Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.hourglass_empty, color: Colors.orange, size: 20),
            SizedBox(width: 10),
            Text("Pendaftaran sedang diverifikasi Admin", style: TextStyle(color: Colors.orange, fontWeight: FontWeight.bold)),
          ],
        ),
      );
    }
    return const SizedBox(); // Jika status 'aktif', banner hilang
  }
}
import 'package:flutter/material.dart';
import '../services/auth_service.dart'; 
import 'dart:convert';

class ClassDetailPage extends StatefulWidget {
  final int classId;
  final String className;
  final String token; 
  
  const ClassDetailPage({
    super.key, 
    required this.classId, 
    required this.className, 
    required this.token
  });

  @override
  State<ClassDetailPage> createState() => _ClassDetailPageState();
}

class _ClassDetailPageState extends State<ClassDetailPage> {
  String status = "none";
  bool isLoading = true;
  final Color spektaRed = const Color(0xFF990000);

  @override
  void initState() {
    super.initState();
    _fetchStatus();
  }

  _fetchStatus() async {
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

  // --- MODIFIKASI FUNGSI DAFTAR (DENGAN LOGIKA GATEKEEPER) ---
  void _handleDaftar() async {
    showDialog(context: context, barrierDismissible: false, builder: (_) => const Center(child: CircularProgressIndicator(color: Color(0xFF990000))));

    try {
      var resp = await AuthService.joinClass(widget.classId, widget.token);
      
      if (!mounted) return;
      Navigator.pop(context); // Tutup Loading

      final responseData = jsonDecode(resp.body);

      if (resp.statusCode == 200) {
        // SUKSES DAFTAR
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(backgroundColor: Colors.green, content: Text("Pendaftaran Berhasil! Menunggu Verifikasi Admin."))
        );
        _fetchStatus(); 
      } 
      else if (resp.statusCode == 403) {
        // TERBLOKIR: DATA BELUM LENGKAP (Gatekeeper)
        showDialog(
          context: context,
          builder: (context) => AlertDialog(
            title: const Text("Profil Belum Lengkap", style: TextStyle(fontWeight: FontWeight.bold, color: Color(0xFF990000))),
            content: Text(responseData['message'] ?? "Silakan lengkapi data Nama Orang Tua, Alamat, dan WA Ortu di menu Akun sebelum mendaftar kelas."),
            actions: [
              TextButton(
                onPressed: () => Navigator.pop(context), 
                child: const Text("OKE, SAYA MENGERTI", style: TextStyle(fontWeight: FontWeight.bold))
              )
            ],
          ),
        );
      } 
      else {
        // ERROR LAINNYA
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(backgroundColor: Colors.red, content: Text(responseData['message'] ?? "Gagal mendaftar, coba lagi."))
        );
      }
    } catch (e) {
      if (!mounted) return;
      Navigator.pop(context);
      print("Error join class: $e");
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(widget.className, style: const TextStyle(fontWeight: FontWeight.bold)),
        backgroundColor: spektaRed,
        foregroundColor: Colors.white,
      ),
      body: isLoading 
        ? Center(child: CircularProgressIndicator(color: spektaRed)) 
        : Column(
            children: [
              _buildStatusBanner(),
              
              Expanded(
                child: ListView.builder(
                  padding: const EdgeInsets.all(15),
                  itemCount: 5, 
                  itemBuilder: (context, index) {
                    bool isLocked = status != 'aktif';
                    return Card(
                      child: ListTile(
                        leading: Icon(
                          isLocked ? Icons.lock : Icons.play_circle_fill, 
                          color: isLocked ? Colors.grey : Colors.green
                        ),
                        title: Text("Materi Pertemuan ke-${index + 1}"),
                        subtitle: Text(isLocked ? "Akses Terkunci" : "Klik untuk menonton video"),
                        onTap: isLocked ? () {
                          ScaffoldMessenger.of(context).showSnackBar(
                            const SnackBar(content: Text("Silakan daftar kelas untuk melihat materi!"))
                          );
                        } : () {
                          // Buka Materi Video
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
        padding: const EdgeInsets.all(20),
        color: Colors.red[50],
        child: Row(
          children: [
            const Expanded(child: Text("Anda belum terdaftar di kelas ini.", style: TextStyle(fontWeight: FontWeight.bold))),
            ElevatedButton(
              style: ElevatedButton.styleFrom(backgroundColor: spektaRed),
              onPressed: _handleDaftar, 
              child: const Text("Daftar Sekarang", style: TextStyle(color: Colors.white))
            )
          ],
        ),
      );
    } else if (status == 'pending') {
      return Container(
        width: double.infinity,
        padding: const EdgeInsets.all(15),
        color: Colors.orange[100],
        child: const Text(
          "⏳ Pendaftaran sedang diverifikasi oleh Admin. Mohon tunggu.", 
          textAlign: TextAlign.center,
          style: TextStyle(fontWeight: FontWeight.bold, color: Colors.orange),
        ),
      );
    }
    return const SizedBox(); 
  }
}
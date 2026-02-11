import 'package:flutter/material.dart';
import '../services/auth_service.dart'; // Pastikan import ini benar
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

  // PERBAIKAN: Nama method disesuaikan dengan AuthService (checkClassStatus)
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

  // PERBAIKAN: Memanggil method joinClass dari AuthService
  void _handleDaftar() async {
    // Tampilkan Loading
    showDialog(context: context, builder: (_) => const Center(child: CircularProgressIndicator()));

    try {
      var resp = await AuthService.joinClass(widget.classId, widget.token);
      Navigator.pop(context); // Tutup Loading

      if (resp.statusCode == 200) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(backgroundColor: Colors.green, content: Text("Pendaftaran Berhasil! Menunggu Verifikasi Admin."))
        );
        _fetchStatus(); // Refresh status di layar
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(backgroundColor: Colors.red, content: Text("Gagal mendaftar, coba lagi."))
        );
      }
    } catch (e) {
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
              // Banner Info Status (Pending/None)
              _buildStatusBanner(),
              
              // Tampilan List Materi (Mata Kuliah: Kontrol Akses)
              Expanded(
                child: ListView.builder(
                  padding: const EdgeInsets.all(15),
                  itemCount: 5, // Contoh 5 materi
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
    return const SizedBox(); // Jika status 'aktif', tidak perlu banner
  }
}
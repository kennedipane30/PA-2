import 'package:flutter/material.dart';
import 'class_detail_page.dart';

class KelasPage extends StatelessWidget {
  final String token; 
  final Map userData; // 1. Tambahkan userData untuk cek kelengkapan profil
  const KelasPage({super.key, required this.token, required this.userData});

  final Color spektaRed = const Color(0xFF990000);
  final Color spektaYellow = const Color(0xFFF1B401);

  @override
  Widget build(BuildContext context) {
    // ... data programs tetap sama seperti sebelumnya ...
    final List<Map<String, dynamic>> programs = [
      {"id": 1, "name": "CALON\nABDI NEGARA", "image": "assets/images/abdi_negara.png", "title": "Program Bimbingan Belajar", "subtitle": "TNI - POLRI - SEKDIN"},
      {"id": 2, "name": "PTN &\nUNHAN", "image": "assets/images/ptn_unhan.png", "title": "Program Bimbingan Belajar", "subtitle": "PERSIAPAN MASUK KAMPUS IMPIAN"},
      {"id": 3, "name": "SMA & SMP\nREGULER", "image": "assets/images/reguler.png", "title": "Program Bimbingan Belajar", "subtitle": "KURSUS HARIAN SISWA"},
      {"id": 4, "name": "SMA\nFAVORIT", "image": "assets/images/favorit.png", "title": "Program Bimbingan Belajar", "subtitle": "DEL - TN - MATAULI - SOPOSURUNG"},
    ];

    return Scaffold(
      backgroundColor: const Color(0xFFF5F5F5),
      appBar: AppBar(
        title: const Text("Pilih Program Kelas", style: TextStyle(fontWeight: FontWeight.bold)),
        backgroundColor: spektaRed,
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      body: ListView.builder(
        padding: const EdgeInsets.all(20),
        itemCount: programs.length,
        itemBuilder: (context, index) => _buildProgramCard(context, programs[index]),
      ),
    );
  }

  // 2. FUNGSI CEK DATA (GATEKEEPER)
  void _checkProfileAndNavigate(BuildContext context, Map<String, dynamic> item) {
    var student = userData['student'];

    // Cek apakah 3 syarat data sudah diisi (Nama Ortu, Alamat/School, WA Ortu)
    bool isComplete = student['parent_name'] != null && 
                      student['school'] != "-" && // "-" adalah default dari Laravel kita tadi
                      student['wa_ortu'] != "-";

    if (isComplete) {
      // JIKA LENGKAP -> Masuk ke Detail
      Navigator.push(
        context,
        MaterialPageRoute(
          builder: (context) => ClassDetailPage(
            classId: item['id'],
            className: item['name'].replaceAll('\n', ' '),
            token: token,
          ),
        ),
      );
    } else {
      // JIKA TIDAK LENGKAP -> Tampilkan Peringatan
      showDialog(
        context: context,
        builder: (context) => AlertDialog(
          title: const Text("Profil Belum Lengkap!"),
          content: const Text("Untuk mendaftar kelas, Anda wajib melengkapi Nama Orang Tua, Alamat, dan WA Ortu di menu Akun."),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(context), 
              child: const Text("OKE", style: TextStyle(color: Color(0xFF990000), fontWeight: FontWeight.bold))
            )
          ],
        ),
      );
    }
  }

  Widget _buildProgramCard(BuildContext context, Map<String, dynamic> item) {
    return Container(
      margin: const EdgeInsets.only(bottom: 25),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(25),
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.1), blurRadius: 10, offset: const Offset(0, 5))],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          ClipRRect(
            borderRadius: const BorderRadius.vertical(top: Radius.circular(25)),
            child: Image.asset(item['image'], height: 220, width: double.infinity, fit: BoxFit.cover),
          ),
          Padding(
            padding: const EdgeInsets.all(20.0),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(item['title'], style: const TextStyle(color: Colors.red, fontSize: 11, fontWeight: FontWeight.bold)),
                const SizedBox(height: 5),
                Text(item['name'], style: TextStyle(color: spektaRed, fontSize: 22, fontWeight: FontWeight.w900, height: 1.1)),
                const SizedBox(height: 20),
                
                // TOMBOL INFO (Panggil Fungsi Cek Profile)
                InkWell(
                  onTap: () => _checkProfileAndNavigate(context, item), // MODIFIKASI DI SINI
                  child: Container(
                    padding: const EdgeInsets.symmetric(vertical: 12, horizontal: 25),
                    decoration: BoxDecoration(color: spektaYellow, borderRadius: BorderRadius.circular(30)),
                    child: const Text("Info Selengkapnya", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 13)),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
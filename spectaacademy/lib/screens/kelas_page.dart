import 'package:flutter/material.dart';
import 'class_detail_page.dart';

class KelasPage extends StatelessWidget {
  final String token; 
  const KelasPage({super.key, required this.token});

  final Color spektaRed = const Color(0xFF990000);
  final Color spektaYellow = const Color(0xFFF1B401);

  @override
  Widget build(BuildContext context) {
    // Data 4 Program Spekta dengan Gambar Aset Lokal
    final List<Map<String, dynamic>> programs = [
      {
        "id": 1,
        "title": "Program Bimbingan Belajar",
        "name": "CALON\nABDI NEGARA",
        "subtitle": "TNI - POLRI - SEKDIN",
        "image": "assets/images/abdi_negara.png", // MENGGUNAKAN ASET LOKAL
      },
      {
        "id": 2,
        "title": "Program Bimbingan Belajar",
        "name": "PTN &\nUNHAN",
        "subtitle": "PERSIAPAN MASUK KAMPUS IMPIAN",
        "image": "assets/images/ptn_unhan.png",
      },
      {
        "id": 3,
        "title": "Program Bimbingan Belajar",
        "name": "SMA & SMP\nREGULER",
        "subtitle": "KURSUS HARIAN SISWA",
        "image": "assets/images/reguler.png",
      },
      {
        "id": 4,
        "title": "Program Bimbingan Belajar",
        "name": "SMA\nFAVORIT",
        "subtitle": "DEL - TN - MATAULI - SOPOSURUNG",
        "image": "assets/images/favorit.png",
      },
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
        itemBuilder: (context, index) {
          final item = programs[index];
          return _buildProgramCard(context, item);
        },
      ),
    );
  }

  Widget _buildProgramCard(BuildContext context, Map<String, dynamic> item) {
    return Container(
      margin: const EdgeInsets.only(bottom: 25),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(25),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.1), 
            blurRadius: 10, 
            offset: const Offset(0, 5)
          )
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Banner Program menggunakan Image.asset
          ClipRRect(
            borderRadius: const BorderRadius.vertical(top: Radius.circular(25)),
            child: Image.asset(
              item['image'],
              height: 220, // Sedikit lebih tinggi agar gambar terlihat jelas
              width: double.infinity,
              fit: BoxFit.cover, // Agar gambar memenuhi area card
              errorBuilder: (context, error, stackTrace) {
                return Container(
                  height: 220,
                  color: Colors.grey[300],
                  child: const Center(child: Text("Gambar tidak ditemukan\ndi assets/images/")),
                );
              },
            ),
          ),
          
          Padding(
            padding: const EdgeInsets.all(20.0),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(item['title'], style: const TextStyle(color: Colors.red, fontSize: 11, fontWeight: FontWeight.bold)),
                const SizedBox(height: 5),
                Text(
                  item['name'],
                  style: TextStyle(color: spektaRed, fontSize: 22, fontWeight: FontWeight.w900, height: 1.1),
                ),
                const SizedBox(height: 5),
                Text(item['subtitle'], style: const TextStyle(color: Colors.grey, fontSize: 10, fontWeight: FontWeight.bold)),
                const SizedBox(height: 20),
                
                // Tombol Navigasi
                InkWell(
                  onTap: () {
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
                  },
                  child: Container(
                    padding: const EdgeInsets.symmetric(vertical: 12, horizontal: 25),
                    decoration: BoxDecoration(
                      color: spektaYellow,
                      borderRadius: BorderRadius.circular(30),
                    ),
                    child: const Text(
                      "Info Selengkapnya",
                      style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 13),
                    ),
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
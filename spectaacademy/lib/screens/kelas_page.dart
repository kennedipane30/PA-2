import 'package:flutter/material.dart';
import 'class_detail_page.dart';

class KelasPage extends StatelessWidget {
  final String token; // Token diperlukan untuk dikirim ke halaman detail
  const KelasPage({super.key, required this.token});

  final Color spektaRed = const Color(0xFF990000);
  final Color spektaYellow = const Color(0xFFF1B401);

  @override
  Widget build(BuildContext context) {
    // Data 4 Program Spekta sesuai gambar referensi
    final List<Map<String, dynamic>> programs = [
      {
        "id": 1,
        "title": "Program Bimbingan Belajar",
        "name": "CALON\nABDI NEGARA",
        "subtitle": "TNI-SEKDIN",
        "image": "https://img.freepik.com/free-vector/gradient-educational-youtube-thumbnail_23-2148918231.jpg",
      },
      {
        "id": 2,
        "title": "Program Bimbingan Belajar",
        "name": "PTN &\nUNHAN",
        "subtitle": "PERSIAPAN KAMPUS",
        "image": "https://img.freepik.com/free-vector/flat-university-concept-background_23-2148184651.jpg",
      },
      {
        "id": 3,
        "title": "Program Bimbingan Belajar",
        "name": "SMA & SMP\nREGULER",
        "subtitle": "KURSUS HARIAN",
        "image": "https://img.freepik.com/free-vector/children-reading-books_23-2147514138.jpg",
      },
      {
        "id": 4,
        "title": "Program Bimbingan Belajar",
        "name": "SMA\nFAVORIT",
        "subtitle": "UNGGULAN & ASRAMA",
        "image": "https://img.freepik.com/free-vector/flat-design-back-school-background_23-2148601831.jpg",
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
      // PERBAIKAN: Menggunakan .only agar tidak eror
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
          // Gambar Banner Program
          ClipRRect(
            borderRadius: const BorderRadius.vertical(top: Radius.circular(25)),
            child: Image.network(
              item['image'],
              height: 180,
              width: double.infinity,
              fit: BoxFit.cover,
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
                
                // Tombol Info Selengkapnya
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
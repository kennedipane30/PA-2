import 'package:flutter/material.dart';
import 'class_detail_page.dart';
import 'edit_profile_page.dart';

class KelasPage extends StatelessWidget {
  final String token;
  final Map userData; 

  const KelasPage({super.key, required this.token, required this.userData});

  final Color spektaRed = const Color(0xFF990000);
  final Color spektaYellow = const Color(0xFFF1B401);

  @override
  Widget build(BuildContext context) {
    final List<Map<String, dynamic>> programs = [
      {
        "id": 1,
        "name": "CALON\nABDI NEGARA",
        "image": "assets/images/abdi_negara.png",
        "title": "Program Bimbingan Belajar",
      },
      {
        "id": 2,
        "name": "PTN &\nUNHAN",
        "image": "assets/images/ptn_unhan.png",
        "title": "Program Bimbingan Belajar",
      },
      {
        "id": 3,
        "name": "SMA & SMP\nREGULER",
        "image": "assets/images/reguler.png",
        "title": "Program Bimbingan Belajar",
      },
      {
        "id": 4,
        "name": "SMA\nFAVORIT",
        "image": "assets/images/favorit.png",
        "title": "Program Bimbingan Belajar",
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
        // PERBAIKAN: Tambah bottom padding (100) agar tidak tertutup navigasi bawah
        padding: const EdgeInsets.fromLTRB(20, 20, 20, 100),
        itemCount: programs.length,
        itemBuilder: (context, index) => _buildProgramCard(context, programs[index]),
      ),
    );
  }

  void _checkProfileAndNavigate(BuildContext context, Map<String, dynamic> item) {
    var student = userData['student'];

    // LOGIKA CEK: Pastikan key sesuai dengan yang dikirim backend (parent_name, school, parent_phone)
    bool isComplete = student != null &&
        student['parent_name'] != null && student['parent_name'] != "-" &&
        student['school'] != null && student['school'] != "-" &&
        // Cek wa_ortu atau parent_phone (antisipasi perbedaan key di backend)
        (student['wa_ortu'] != "-" || (student['parent_phone'] != null && student['parent_phone'] != "-"));

    if (isComplete) {
      Navigator.push(
        context,
        MaterialPageRoute(
          builder: (context) => ClassDetailPage(
            classId: item['id'],
            className: item['name'].replaceAll('\n', ' '),
            token: token,
            userData: userData,
          ),
        ),
      );
    } else {
      showDialog(
        context: context,
        barrierDismissible: false,
        builder: (context) => AlertDialog(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
          title: Row(
            children: [
              Icon(Icons.warning_amber_rounded, color: spektaRed),
              const SizedBox(width: 10),
              const Text("Profil Belum Lengkap", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 18)),
            ],
          ),
          content: const Text("Anda wajib melengkapi data Nama Orang Tua, Alamat, dan WA Ortu sebelum mendaftar kelas ini."),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(context),
              child: Text("BATAL", style: TextStyle(color: Colors.grey[600], fontWeight: FontWeight.bold)),
            ),
            ElevatedButton(
              style: ElevatedButton.styleFrom(
                backgroundColor: spektaRed,
                foregroundColor: Colors.white,
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
              ),
              onPressed: () async {
                Navigator.pop(context);
                
                final result = await Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (context) => EditProfilePage(
                      userData: userData,
                      token: token,
                    ),
                  ),
                );

                if (result == true) {
                  ScaffoldMessenger.of(context).showSnackBar(
                    const SnackBar(content: Text("Profil diperbarui! Silakan klik kembali kelas yang diinginkan."))
                  );
                }
              },
              child: const Text("LENGKAPI SEKARANG", style: TextStyle(fontWeight: FontWeight.bold)),
            ),
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
                InkWell(
                  onTap: () => _checkProfileAndNavigate(context, item),
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
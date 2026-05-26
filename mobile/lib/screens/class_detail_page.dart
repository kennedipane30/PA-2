import 'package:flutter/material.dart';
import 'dart:convert';
import '../services/auth_service.dart';
import 'tryout_detail_page.dart';

// --- PERBAIKAN IMPORT ---
// Jika file pendaftaran ada di folder yang sama, gunakan:
import 'pendaftaran_kelas_page.dart'; 
// Jika tetap merah, klik kanan file 'pendaftaran_kelas_page.dart' -> Copy Relative Path -> Paste di sini.

class ClassDetailPage extends StatefulWidget {
  final int classId;
  final String className;
  final String token;
  final Map userData;

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
  String status = "none";
  List materi = [];
  List tryouts = [];
  bool isLoading = true;
  final Color spektaRed = const Color(0xFF990000);

  @override
  void initState() {
    super.initState();
    _fetchDetail();
  }

  Future<void> _fetchDetail() async {
    try {
      var resp = await AuthService.getClassContent(widget.classId, widget.token);
      if (resp.statusCode == 200) {
        var data = jsonDecode(resp.body);
        if (mounted) {
          setState(() {
            status = data['enroll_status'] ?? "none";
            materi = data['materi'] ?? [];
            tryouts = data['tryouts'] ?? [];
          });
        }
      }
    } catch (e) {
      debugPrint("Error fetch detail: $e");
    } finally {
      if (mounted) setState(() => isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    bool isRegistered = status == 'aktif';

    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: Text(widget.className, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
        backgroundColor: spektaRed,
        foregroundColor: Colors.white,
      ),
      body: isLoading
          ? Center(child: CircularProgressIndicator(color: spektaRed))
          : SingleChildScrollView(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  if (status == 'pending') _buildStatusBanner(),
                  
                  const Padding(
                    padding: EdgeInsets.all(20),
                    child: Text("Detail Program", style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold)),
                  ),

                  // Informasi Promo (Agar User Tertarik)
                  if (!isRegistered && status == 'none') _buildPromoBanner(),

                  if (tryouts.isNotEmpty) ...[
                    const Padding(
                      padding: EdgeInsets.symmetric(horizontal: 20),
                      child: Text("Simulasi Try-Out", style: TextStyle(fontWeight: FontWeight.bold)),
                    ),
                    _buildListContent(tryouts, isRegistered, Icons.assignment, Colors.orange, true),
                  ],

                  const Padding(
                    padding: EdgeInsets.symmetric(horizontal: 20, vertical: 10),
                    child: Text("Materi Video Pembelajaran", style: TextStyle(fontWeight: FontWeight.bold)),
                  ),
                  _buildListContent(materi, isRegistered, Icons.play_circle_fill, Colors.green, false),
                  
                  const SizedBox(height: 120), 
                ],
              ),
            ),
      bottomNavigationBar: !isRegistered && status == 'none'
          ? _buildBottomAction()
          : null,
    );
  }

  Widget _buildPromoBanner() {
    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 20, vertical: 10),
      padding: const EdgeInsets.all(15),
      decoration: BoxDecoration(color: Colors.amber[50], borderRadius: BorderRadius.circular(15), border: Border.all(color: Colors.amber)),
      child: const Row(
        children: [
          Icon(Icons.local_offer, color: Colors.orange),
          SizedBox(width: 15),
          Expanded(child: Text("Gunakan KODE PROMO untuk potongan harga spesial!", style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold))),
        ],
      ),
    );
  }

  Widget _buildBottomAction() {
    return Container(
      height: 100,
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(color: Colors.white, boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 10)]),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Text("Harga Investasi", style: TextStyle(color: Colors.grey, fontSize: 11)),
              // --- PERBAIKAN FONTWEIGHT.BLACK MENJADI W900 ---
              Text("Rp 900.000", style: TextStyle(color: spektaRed, fontSize: 20, fontWeight: FontWeight.w900)),
            ],
          ),
          ElevatedButton(
            style: ElevatedButton.styleFrom(backgroundColor: spektaRed, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15))),
            onPressed: () {
              // --- NAVIGASI KE HALAMAN PENDAFTARAN ---
              Navigator.push(context, MaterialPageRoute(builder: (context) => PendaftaranKelasPage(
                classId: widget.classId,
                className: widget.className,
                token: widget.token,
                userData: widget.userData,
              )));
            }, 
            child: const Text("DAFTAR SEKARANG", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
          )
        ],
      ),
    );
  }

  Widget _buildListContent(List items, bool isRegistered, IconData activeIcon, Color activeColor, bool isTryout) {
    return ListView.builder(
      shrinkWrap: true, physics: const NeverScrollableScrollPhysics(),
      itemCount: items.length,
      itemBuilder: (context, index) => ListTile(
        leading: Icon(isRegistered ? activeIcon : Icons.lock, color: isRegistered ? activeColor : Colors.grey),
        title: Text(items[index]['title'] ?? "Materi"),
      ),
    );
  }

  Widget _buildStatusBanner() {
    return Container(width: double.infinity, padding: const EdgeInsets.all(15), color: Colors.orange[50], child: const Text("⌛ Sedang diverifikasi admin", textAlign: TextAlign.center, style: TextStyle(color: Colors.orange, fontWeight: FontWeight.bold)));
  }
}
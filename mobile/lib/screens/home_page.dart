import 'package:flutter/material.dart';
import 'dart:async';
import 'dart:convert';
import 'package:http/http.dart' as http;
// 1. PASTIKAN ANDA MENGIMPORT HALAMAN NOTIFIKASI
import 'notifikasi_page.dart'; 

class HomePage extends StatefulWidget {
  final String userName;
  const HomePage({super.key, required this.userName});

  @override
  State<HomePage> createState() => _HomePageState();
}

class _HomePageState extends State<HomePage> {
  final Color spektaRed = const Color(0xFF990000);
  
  List galeriData = [];
  late PageController _pageController;
  int _currentPage = 0;
  Timer? _timer;

  @override
  void initState() {
    super.initState();
    _pageController = PageController(initialPage: 0);
    fetchGaleri();
  }

  @override
  void dispose() {
    _timer?.cancel();
    _pageController.dispose();
    super.dispose();
  }

  Future<void> fetchGaleri() async {
    try {
      final response = await http.get(Uri.parse('http://10.0.2.2:8000/api/galeri'));
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        setState(() {
          galeriData = data['data'] ?? [];
        });
        if (galeriData.isNotEmpty) {
          _startAutoSlide();
        }
      }
    } catch (e) {
      debugPrint("Error fetching galeri: $e");
    }
  }

  void _startAutoSlide() {
    _timer = Timer.periodic(const Duration(seconds: 10), (Timer timer) {
      if (_currentPage < galeriData.length - 1) {
        _currentPage++;
      } else {
        _currentPage = 0;
      }

      if (_pageController.hasClients) {
        _pageController.animateToPage(
          _currentPage,
          duration: const Duration(milliseconds: 900),
          curve: Curves.easeInOut,
        );
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: SingleChildScrollView(
        child: Column(
          children: [
            // --- HEADER MERAH ---
            Container(
              width: double.infinity,
              padding: const EdgeInsets.only(top: 60, left: 25, right: 15, bottom: 35),
              decoration: BoxDecoration(
                color: spektaRed,
                borderRadius: const BorderRadius.only(
                  bottomLeft: Radius.circular(35),
                  bottomRight: Radius.circular(35),
                ),
              ),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Expanded(
                    child: Text("Hai, ${widget.userName}", 
                      style: const TextStyle(color: Colors.white, fontSize: 22, fontWeight: FontWeight.bold)),
                  ),
                  // 2. MODIFIKASI BAGIAN INI: Menggunakan IconButton agar bisa diklik
                  Row(
                    children: [
                      IconButton(
                        onPressed: () {
                          // NAVIGASI KE HALAMAN NOTIFIKASI
                          Navigator.push(
                            context,
                            MaterialPageRoute(builder: (context) => const NotifikasiPage()),
                          );
                        },
                        icon: const Icon(Icons.notifications_none, color: Colors.white, size: 28),
                      ),
                      IconButton(
                        onPressed: () {
                          // Tambahkan aksi untuk bookmark di sini
                        },
                        icon: const Icon(Icons.bookmark_border, color: Colors.white, size: 28),
                      ),
                    ],
                  )
                ],
              ),
            ),

            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 20.0, vertical: 25.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text("Layanan Spekta", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
                  const SizedBox(height: 15),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      _buildMenuIcon(Icons.play_circle_fill, "Materi", Colors.purple),
                      _buildMenuIcon(Icons.edit_document, "Ujian", Colors.orange),
                      _buildMenuIcon(Icons.bolt, "Latihan", Colors.indigo),
                      _buildMenuIcon(Icons.emoji_events, "Try-Out", Colors.amber),
                    ],
                  ),

                  const SizedBox(height: 35),

                  Container(
                    width: double.infinity,
                    padding: const EdgeInsets.all(20),
                    decoration: BoxDecoration(
                      gradient: const LinearGradient(
                        colors: [Color(0xFF990000), Color(0xFFD32F2F)],
                        begin: Alignment.topLeft,
                        end: Alignment.bottomRight,
                      ),
                      borderRadius: BorderRadius.circular(20),
                    ),
                    child: Row(
                      children: [
                        const Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text("TIM GERCEP SIAPIN UTBK", 
                                style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 14)),
                              SizedBox(height: 5),
                              Text("Paket Hanya 900rb", 
                                style: TextStyle(color: Colors.yellow, fontSize: 18, fontWeight: FontWeight.w900)),
                              SizedBox(height: 10),
                              Text("Cuma Hari Ini!", style: TextStyle(color: Colors.white, fontSize: 10)),
                            ],
                          ),
                        ),
                        Image.network('https://cdn-icons-png.flaticon.com/512/3429/3429153.png', height: 70),
                      ],
                    ),
                  ),

                  const SizedBox(height: 35),

                  if (galeriData.isNotEmpty) ...[
                    const Center(child: Text("Kegiatan Spekta Terbaru", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16))),
                    const SizedBox(height: 15),
                    SizedBox(
                      height: 240,
                      child: PageView.builder(
                        controller: _pageController,
                        itemCount: galeriData.length,
                        onPageChanged: (index) => _currentPage = index,
                        itemBuilder: (context, index) {
                          var item = galeriData[index];
                          String imageUrl = 'http://10.0.2.2:8000/storage/${item['foto']}';
                          
                          return Column(
                            children: [
                              Text(item['judul'], 
                                style: const TextStyle(fontWeight: FontWeight.bold, color: Color(0xFF990000)),
                                textAlign: TextAlign.center),
                              const SizedBox(height: 12),
                              Expanded(
                                child: Container(
                                  margin: const EdgeInsets.symmetric(horizontal: 10),
                                  decoration: BoxDecoration(
                                    borderRadius: BorderRadius.circular(20),
                                    boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 8, offset: const Offset(0, 4))],
                                  ),
                                  child: ClipRRect(
                                    borderRadius: BorderRadius.circular(20),
                                    child: Image.network(
                                      imageUrl,
                                      fit: BoxFit.cover,
                                      width: double.infinity,
                                      loadingBuilder: (context, child, loadingProgress) {
                                        if (loadingProgress == null) return child;
                                        return const Center(child: CircularProgressIndicator(color: Color(0xFF990000)));
                                      },
                                      errorBuilder: (context, error, stackTrace) {
                                        return Container(
                                          color: Colors.grey[200],
                                          child: const Center(child: Icon(Icons.broken_image, size: 40, color: Colors.grey)),
                                        );
                                      },
                                    ),
                                  ),
                                ),
                              ),
                            ],
                          );
                        },
                      ),
                    ),
                  ],
                  const SizedBox(height: 20),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildMenuIcon(IconData icon, String label, Color color) {
    return Column(
      children: [
        Container(
          padding: const EdgeInsets.all(14),
          decoration: BoxDecoration(
            color: color.withOpacity(0.1),
            borderRadius: BorderRadius.circular(18),
          ),
          child: Icon(icon, color: color, size: 30),
        ),
        const SizedBox(height: 8),
        Text(label, style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w600, color: Colors.black87)),
      ],
    );
  }
}
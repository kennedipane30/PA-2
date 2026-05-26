import 'package:flutter/material.dart';
import 'dart:async';
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'notifikasi_page.dart'; 

class HomePage extends StatefulWidget {
  final String userName;
  final String token; // Tambahkan token
  final Map userData; // Tambahkan userData

  const HomePage({
    super.key, 
    required this.userName, 
    required this.token, 
    required this.userData
  });

  @override
  State<HomePage> createState() => _HomePageState();
}

class _HomePageState extends State<HomePage> {
  final Color spektaRed = const Color(0xFF990000); 
  final Color bgColor = const Color(0xFFF8F9FA); 
  
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

  // --- FUNGSI NAVIGASI MENU ---
  void _navigateTo(Widget page) {
    Navigator.push(
      context,
      MaterialPageRoute(builder: (context) => page),
    );
  }

  // --- FUNGSI UNTUK MENU YANG BELUM ADA HALAMANNYA ---
  void _showComingSoon(String menuName) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text("Fitur $menuName akan segera hadir! 🚀"),
        backgroundColor: spektaRed,
        behavior: SnackBarBehavior.floating,
      ),
    );
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
      backgroundColor: bgColor, 
      body: RefreshIndicator( // Tambahkan fitur tarik untuk refresh data
        onRefresh: fetchGaleri,
        color: spektaRed,
        child: SingleChildScrollView(
          physics: const AlwaysScrollableScrollPhysics(),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // --- HEADER ---
              Container(
                width: double.infinity,
                padding: const EdgeInsets.fromLTRB(25, 60, 15, 35),
                decoration: BoxDecoration(
                  color: spektaRed,
                  borderRadius: const BorderRadius.only(
                    bottomLeft: Radius.circular(30),
                    bottomRight: Radius.circular(30),
                  ),
                ),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Text("Selamat Datang,", style: TextStyle(color: Colors.white70, fontSize: 14)),
                        const SizedBox(height: 4),
                        Text(widget.userName, style: const TextStyle(color: Colors.white, fontSize: 22, fontWeight: FontWeight.bold)),
                      ],
                    ),
                    IconButton(
                      onPressed: () => _navigateTo(const NotifikasiPage()),
                      icon: const Icon(Icons.notifications_none_rounded, color: Colors.white, size: 28),
                    ),
                  ],
                ),
              ),

              // --- LAYANAN SPEKTA (GRID MENU) ---
              const Padding(
                padding: EdgeInsets.fromLTRB(25, 30, 25, 15),
                child: Text("Layanan Spekta", style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: Color(0xFF2D3436))),
              ),

              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 20),
                child: GridView.count(
                  shrinkWrap: true,
                  physics: const NeverScrollableScrollPhysics(),
                  crossAxisCount: 3,
                  mainAxisSpacing: 15,
                  crossAxisSpacing: 10,
                  childAspectRatio: 0.9,
                  children: [
                    _buildMenuIcon(Icons.menu_book_rounded, "Materi Belajar", Colors.orange, () {
                      // Ganti dengan halaman Materi Anda
                      _showComingSoon("Materi Belajar");
                    }),
                    _buildMenuIcon(Icons.person_search_rounded, "Dedicated Tutor", Colors.blue, () {
                      _showComingSoon("Dedicated Tutor");
                    }),
                    _buildMenuIcon(Icons.assignment_outlined, "Bank Soal", Colors.green, () {
                      _showComingSoon("Bank Soal");
                    }),
                    _buildMenuIcon(Icons.info_outline_rounded, "Tentang Spekta", Colors.lightBlue, () {
                      _showComingSoon("Tentang Spekta");
                    }),
                    _buildMenuIcon(Icons.chat_bubble_outline_rounded, "Konsultasi", Colors.purple, () {
                      _showComingSoon("Konsultasi");
                    }),
                    _buildMenuIcon(Icons.headset_mic_outlined, "Pusat Bantuan", Colors.teal, () {
                      _showComingSoon("Pusat Bantuan");
                    }),
                  ],
                ),
              ),

              const SizedBox(height: 25),

              // --- GALERI ---
              if (galeriData.isNotEmpty) ...[
                const Padding(
                  padding: EdgeInsets.symmetric(horizontal: 25),
                  child: Text("Kegiatan Terbaru", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16, color: Color(0xFF2D3436))),
                ),
                const SizedBox(height: 15),
                SizedBox(
                  height: 230,
                  child: PageView.builder(
                    controller: _pageController,
                    itemCount: galeriData.length,
                    onPageChanged: (index) => _currentPage = index,
                    itemBuilder: (context, index) {
                      var item = galeriData[index];
                      String imageUrl = 'http://10.0.2.2:8000/storage/${item['foto']}';
                      
                      return Container(
                        margin: const EdgeInsets.symmetric(horizontal: 20, vertical: 5),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(20),
                          border: Border.all(color: const Color(0xFFFFD1DC).withOpacity(0.5), width: 1), 
                          boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.03), blurRadius: 10, offset: const Offset(0, 5))],
                        ),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Expanded(
                              child: ClipRRect(
                                borderRadius: const BorderRadius.vertical(top: Radius.circular(20)),
                                child: Image.network(imageUrl, width: double.infinity, fit: BoxFit.cover),
                              ),
                            ),
                            Padding(
                              padding: const EdgeInsets.all(12),
                              child: Text(item['judul'] ?? '', style: TextStyle(fontWeight: FontWeight.bold, color: spektaRed, fontSize: 14), maxLines: 1, overflow: TextOverflow.ellipsis),
                            ),
                          ],
                        ),
                      );
                    },
                  ),
                ),
              ],
              const SizedBox(height: 50),
            ],
          ),
        ),
      ),
    );
  }

  // MODIFIKASI HELPER: Tambahkan onTap
  Widget _buildMenuIcon(IconData icon, String label, Color iconColor, VoidCallback onTap) {
    return InkWell( // Menggunakan InkWell agar ada efek sentuhan (ripple)
      onTap: onTap,
      borderRadius: BorderRadius.circular(18),
      child: Column(
        children: [
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: iconColor.withOpacity(0.1),
              borderRadius: BorderRadius.circular(18),
            ),
            child: Icon(icon, color: iconColor, size: 26),
          ),
          const SizedBox(height: 8),
          Text(label, textAlign: TextAlign.center, style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w600, color: Color(0xFF2D3436))),
        ],
      ),
    );
  }
}
import 'package:flutter/material.dart';

class HomePage extends StatelessWidget {
  final String userName;
  const HomePage({super.key, required this.userName});

  final Color spektaRed = const Color(0xFF990000);

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: SingleChildScrollView(
        child: Column(
          children: [
            // --- HEADER (Bagian Merah Spekta) ---
            Container(
              width: double.infinity,
              padding: const EdgeInsets.only(top: 60, left: 20, right: 20, bottom: 25),
              decoration: BoxDecoration(
                color: spektaRed,
                borderRadius: const BorderRadius.only(
                  bottomLeft: Radius.circular(30),
                  bottomRight: Radius.circular(30),
                ),
              ),
              child: Column(
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text("Hai, $userName", 
                            style: const TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold)),
                          const Text("Kelas 12 IPA", 
                            style: TextStyle(color: Colors.white70, fontSize: 12)),
                        ],
                      ),
                      const Row(
                        children: [
                          Icon(Icons.notifications_none, color: Colors.white),
                          SizedBox(width: 15),
                          Icon(Icons.bookmark_border, color: Colors.white),
                        ],
                      )
                    ],
                  ),
                  const SizedBox(height: 20),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 15),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: const TextField(
                      decoration: InputDecoration(
                        hintText: "Kamu mau belajar apa hari ini?",
                        hintStyle: TextStyle(color: Colors.grey, fontSize: 13),
                        border: InputBorder.none,
                        icon: Icon(Icons.search, color: Colors.grey, size: 20),
                      ),
                    ),
                  ),
                ],
              ),
            ),

            // --- BODY CONTENT ---
            Padding(
              padding: const EdgeInsets.all(20.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Banner Promo (Gradient Red)
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
                                style: TextStyle(color: Colors.yellow, fontSize: 18, fontWeight: FontWeight.w900)), // SUDAH DIPERBAIKI KE w900
                              SizedBox(height: 10),
                              Text("Cuma Hari Ini!", style: TextStyle(color: Colors.white, fontSize: 10)),
                            ],
                          ),
                        ),
                        Image.network('https://cdn-icons-png.flaticon.com/512/3429/3429153.png', height: 80),
                      ],
                    ),
                  ),

                  const SizedBox(height: 25), // JARAK ANTARA BANNER DAN MENU
                  
                  const Text("Layanan Spekta", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
                  
                  const SizedBox(height: 20),

                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      _buildMenuIcon(Icons.play_circle_fill, "Materi", Colors.purple),
                      _buildMenuIcon(Icons.edit_document, "Ujian", Colors.orange),
                      _buildMenuIcon(Icons.bolt, "Latihan", Colors.indigo),
                      _buildMenuIcon(Icons.emoji_events, "Try-Out", Colors.amber),
                    ],
                  ),

                  const SizedBox(height: 30),
                  const Text("Promo Terbaru", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
                  const SizedBox(height: 15),
                  Container(
                    height: 100,
                    width: double.infinity,
                    decoration: BoxDecoration(
                      color: Colors.grey[100],
                      borderRadius: BorderRadius.circular(15),
                      border: Border.all(color: Colors.grey[300]!),
                    ),
                    child: const Center(child: Text("Promo dari Admin muncul di sini", style: TextStyle(color: Colors.grey))),
                  )
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
          padding: const EdgeInsets.all(12),
          decoration: BoxDecoration(
            color: color.withOpacity(0.1),
            borderRadius: BorderRadius.circular(15),
          ),
          child: Icon(icon, color: color, size: 28),
        ),
        const SizedBox(height: 8),
        Text(label, style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w600)),
      ],
    );
  }
}
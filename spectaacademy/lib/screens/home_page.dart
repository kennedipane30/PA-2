import 'package:flutter/material.dart';

class HomePage extends StatelessWidget {
  const HomePage({super.key});

  @override
  Widget build(BuildContext context) {
    const Color spektaRed = Color(0xFF990000);

    return Scaffold(
      backgroundColor: Colors.white,
      body: SingleChildScrollView(
        child: Column(
          children: [
            // Header Melengkung Merah
            Container(
              height: 200,
              padding: const EdgeInsets.all(25),
              decoration: const BoxDecoration(
                color: spektaRed,
                borderRadius: BorderRadius.only(bottomRight: Radius.circular(50)),
              ),
              child: const SafeArea(
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text("Spekta Academy", style: TextStyle(color: Colors.white, fontSize: 24, fontWeight: FontWeight.bold)),
                        Text("Solusi Belajar Masa Kini", style: TextStyle(color: Colors.white70)),
                      ],
                    ),
                    Icon(Icons.stars, color: Colors.white, size: 40),
                  ],
                ),
              ),
            ),
            // Konten
            Padding(
              padding: const EdgeInsets.all(20),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text("Promo Hari Ini", style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
                  const SizedBox(height: 10),
                  Container(
                    height: 120,
                    width: double.infinity,
                    decoration: BoxDecoration(
                      color: spektaRed.withOpacity(0.1),
                      borderRadius: BorderRadius.circular(15),
                      border: Border.all(color: spektaRed),
                    ),
                    child: const Center(child: Text("DISKON 50% UNTUK SISWA BARU!", style: TextStyle(color: spektaRed, fontWeight: FontWeight.bold))),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}
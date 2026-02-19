import 'package:flutter/material.dart';
import 'home_page.dart';
import 'kelas_page.dart';
import 'notifikasi_page.dart';
import 'akun_page.dart';

class MainScreen extends StatefulWidget {
  final String userName; 
  final String token; 
  final Map userProfileData; // 1. Tambahkan variabel Map untuk menampung data profil lengkap

  const MainScreen({
    super.key, 
    required this.userName, 
    required this.token,
    required this.userProfileData // 2. Tambahkan ke constructor
  });

  @override
  State<MainScreen> createState() => _MainScreenState();
}

class _MainScreenState extends State<MainScreen> {
  int _selectedIndex = 0;
  final Color spektaRed = const Color(0xFF990000);

  // Fungsi untuk menampilkan halaman sesuai index
  Widget _getBody() {
    switch (_selectedIndex) {
      case 0:
        return HomePage(userName: widget.userName);
      case 1:
        // 3. Kirim token DAN userData ke KelasPage untuk cek kelengkapan profil
        return KelasPage(token: widget.token, userData: widget.userProfileData); 
      case 2:
        return const NotifikasiPage();
      case 3:
        // 4. Kirim userData ke AkunPage agar Gmail & No HP muncul otomatis
        return AkunPage(token: widget.token, userData: widget.userProfileData);
      default:
        return HomePage(userName: widget.userName);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: _getBody(),
      bottomNavigationBar: BottomNavigationBar(
        type: BottomNavigationBarType.fixed,
        currentIndex: _selectedIndex,
        selectedItemColor: spektaRed,
        unselectedItemColor: Colors.grey,
        selectedLabelStyle: const TextStyle(fontWeight: FontWeight.bold, fontSize: 12),
        onTap: (index) => setState(() => _selectedIndex = index),
        items: const [
          BottomNavigationBarItem(icon: Icon(Icons.home_filled), label: 'Beranda'),
          BottomNavigationBarItem(icon: Icon(Icons.class_outlined), label: 'Kelas'),
          BottomNavigationBarItem(icon: Icon(Icons.notifications_none), label: 'Notifikasi'),
          BottomNavigationBarItem(icon: Icon(Icons.person_outline), label: 'Akun'),
        ],
      ),
    );
  }
}
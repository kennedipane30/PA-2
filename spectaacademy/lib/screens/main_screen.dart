import 'package:flutter/material.dart';
import 'home_page.dart';
import 'kelas_page.dart';
import 'notifikasi_page.dart';
import 'akun_page.dart';

class MainScreen extends StatefulWidget {
  final String userName; // Menerima nama dari Login/OTP
  const MainScreen({super.key, required this.userName});

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
        return HomePage(userName: widget.userName); // Kirim nama ke Home
      case 1:
        return const KelasPage();
      case 2:
        return const NotifikasiPage();
      case 3:
        return const AkunPage();
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
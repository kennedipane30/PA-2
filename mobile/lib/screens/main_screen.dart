import 'package:flutter/material.dart';
import 'home_page.dart';
import 'jadwal_page.dart';
import 'kelas_page.dart';
import 'akun_page.dart';

class MainScreen extends StatefulWidget {
  final String userName; 
  final String token; 
  final Map userProfileData;

  const MainScreen({
    super.key, 
    required this.userName, 
    required this.token,
    required this.userProfileData
  });

  @override
  State<MainScreen> createState() => _MainScreenState();
}

class _MainScreenState extends State<MainScreen> {
  int _selectedIndex = 0;
  final Color spektaRed = const Color(0xFF990000); // Warna Merah Spekta Utama

  // Fungsi navigasi untuk berpindah tab
  void _onItemTapped(int index) {
    setState(() {
      _selectedIndex = index;
    });
  }

  // Memilih halaman berdasarkan index yang aktif
  Widget _getBody() {
    switch (_selectedIndex) {
      case 0: 
        // SEKARANG DATA TERKONEKSI PENUH KE HOME PAGE
        return HomePage(
          userName: widget.userName,
          token: widget.token,
          userData: widget.userProfileData,
        );
      case 1: 
        return KelasPage(
          token: widget.token, 
          userData: widget.userProfileData
        );
      case 2: 
        return JadwalPage(token: widget.token);
      case 3: 
        return AkunPage(
          token: widget.token, 
          userData: widget.userProfileData
        );
      default: 
        return HomePage(
          userName: widget.userName,
          token: widget.token,
          userData: widget.userProfileData,
        );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: _getBody(),

      // --- BOTTOM NAVIGATION BAR (Versi Bersih/Clean) ---
      // Menghapus FloatingActionButton agar tidak menumpuk dan menghalangi konten
      bottomNavigationBar: Container(
        decoration: BoxDecoration(
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.05),
              blurRadius: 10,
              offset: const Offset(0, -5),
            ),
          ],
        ),
        child: BottomNavigationBar(
          currentIndex: _selectedIndex,
          onTap: _onItemTapped,
          type: BottomNavigationBarType.fixed, // Memastikan 4 ikon berjarak rata
          backgroundColor: Colors.white,
          selectedItemColor: spektaRed,
          unselectedItemColor: Colors.grey.shade400,
          showSelectedLabels: true,
          showUnselectedLabels: true,
          selectedLabelStyle: const TextStyle(fontWeight: FontWeight.bold, fontSize: 11),
          unselectedLabelStyle: const TextStyle(fontSize: 11),
          items: const [
            BottomNavigationBarItem(
              icon: Icon(Icons.grid_view_rounded),
              label: "Beranda",
            ),
            BottomNavigationBarItem(
              icon: Icon(Icons.auto_stories_rounded),
              label: "Kelas",
            ),
            BottomNavigationBarItem(
              icon: Icon(Icons.calendar_month_rounded),
              label: "Jadwal",
            ),
            BottomNavigationBarItem(
              icon: Icon(Icons.account_circle_rounded),
              label: "Akun",
            ),
          ],
        ),
      ),
    );
  }
}
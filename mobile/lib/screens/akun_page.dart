import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../services/auth_service.dart';
import 'edit_profile_page.dart';
import 'login_page.dart';

class AkunPage extends StatelessWidget {
  final String token;
  final Map userData;

  const AkunPage({
    super.key, 
    required this.token, 
    required this.userData
  });

  // FUNGSI KONFIRMASI LOGOUT
  void _confirmLogout(BuildContext context) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text("Keluar Akun"),
        content: const Text("Apakah Anda yakin ingin keluar?"),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context), child: const Text("Batal")),
          ElevatedButton(
            style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF990000)),
            onPressed: () async {
              SharedPreferences prefs = await SharedPreferences.getInstance();
              await prefs.clear(); // Hapus Token di HP

              try {
                await AuthService.logout(token); // Matikan token di server
              } catch (e) {
                debugPrint("Error: $e");
              }

              if (!context.mounted) return;
              Navigator.pushAndRemoveUntil(
                context,
                MaterialPageRoute(builder: (context) => const LoginPage()),
                (route) => false,
              );
            },
            child: const Text("Keluar", style: TextStyle(color: Colors.white)),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    const Color spektaRed = Color(0xFF990000);

    return Scaffold(
      // --- 1. TAMBAHKAN IKON LOGOUT DI APPBAR (POJOK KANAN ATAS) ---
      appBar: AppBar(
        backgroundColor: spektaRed,
        elevation: 0,
        title: const Text("Profil Saya", style: TextStyle(color: Colors.white)),
        actions: [
          IconButton(
            icon: const Icon(Icons.logout, color: Colors.white),
            onPressed: () => _confirmLogout(context),
            tooltip: "Keluar Akun",
          ),
        ],
      ),
      body: Column(
        children: [
          // Header Profil
          Container(
            width: double.infinity,
            padding: const EdgeInsets.only(bottom: 40),
            decoration: const BoxDecoration(
              color: spektaRed,
              borderRadius: BorderRadius.only(
                bottomLeft: Radius.circular(50), 
                bottomRight: Radius.circular(50)
              ),
            ),
            child: Column(
              children: [
                const CircleAvatar(
                  radius: 50,
                  backgroundColor: Colors.white,
                  child: Icon(Icons.person, size: 60, color: spektaRed),
                ),
                const SizedBox(height: 15),
                Text(
                  userData['name'] ?? "Siswa Spekta",
                  style: const TextStyle(color: Colors.white, fontSize: 22, fontWeight: FontWeight.bold),
                ),
                Text(
                  userData['email'] ?? "",
                  style: const TextStyle(color: Colors.white70, fontSize: 14),
                ),
              ],
            ),
          ),

          const SizedBox(height: 20),

          // Menu List
          ListTile(
            leading: const Icon(Icons.edit_note, color: spektaRed),
            title: const Text("Lengkapi Data Diri", style: TextStyle(fontWeight: FontWeight.bold)),
            subtitle: const Text("Nama Ortu, Alamat, WA Ortu"),
            trailing: const Icon(Icons.arrow_forward_ios, size: 16),
            onTap: () {
              Navigator.push(
                context,
                MaterialPageRoute(
                  builder: (context) => EditProfilePage(userData: userData, token: token),
                ),
              );
            },
          ),

          const Divider(),

          ListTile(
            leading: const Icon(Icons.phone_android, color: spektaRed),
            title: const Text("Nomor WhatsApp"),
            subtitle: Text(userData['phone'] ?? "-"),
          ),

          const Divider(),

          // --- 2. TAMBAHKAN IKON LOGOUT DI DAFTAR MENU (LEBIH JELAS) ---
          ListTile(
            leading: const Icon(Icons.logout_rounded, color: Colors.red),
            title: const Text(
              "Logout / Keluar", 
              style: TextStyle(color: Colors.red, fontWeight: FontWeight.bold)
            ),
            onTap: () => _confirmLogout(context),
          ),

          const Spacer(),

          // Tetap ada tombol besar di bawah jika Anda suka
          Padding(
            padding: const EdgeInsets.all(25),
            child: ElevatedButton.icon(
              style: ElevatedButton.styleFrom(
                backgroundColor: spektaRed,
                minimumSize: const Size(double.infinity, 55),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
              ),
              onPressed: () => _confirmLogout(context),
              icon: const Icon(Icons.power_settings_new, color: Colors.white), // Ikon Logout
              label: const Text("KELUAR AKUN", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
            ),
          ),
          const SizedBox(height: 10),
        ],
      ),
    );
  }
}
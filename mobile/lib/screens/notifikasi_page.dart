import 'package:flutter/material.dart';

class NotifikasiPage extends StatelessWidget {
  const NotifikasiPage({super.key});

  @override
  Widget build(BuildContext context) {
    // Simulasi data dari database (nanti diganti API)
    final List<Map<String, String>> notifications = [
      {
        "title": "Pembayaran Berhasil!",
        "desc": "Selamat, paket UTBK kamu sudah aktif. Yuk mulai belajar!",
        "time": "2 jam yang lalu"
      },
      {
        "title": "Try-Out Akan Dimulai",
        "desc": "Jangan lupa, Try-Out Akbar dimulai besok jam 08:00 WIB.",
        "time": "1 hari yang lalu"
      },
    ];

    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: const Text("Notifikasi", style: TextStyle(color: Colors.black, fontWeight: FontWeight.bold)),
        backgroundColor: Colors.white,
        elevation: 0.5,
        iconTheme: const IconThemeData(color: Colors.black),
      ),
      body: notifications.isEmpty
          ? _buildEmptyState() // Jika kosong tampilkan icon abu-abu
          : _buildNotificationList(notifications), // Jika ada data tampilkan list
    );
  }

  Widget _buildEmptyState() {
    return const Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(Icons.notifications_none_outlined, size: 80, color: Colors.grey),
          SizedBox(height: 10),
          Text("Belum ada notifikasi baru", style: TextStyle(color: Colors.grey)),
        ],
      ),
    );
  }

  Widget _buildNotificationList(List<Map<String, String>> data) {
    return ListView.separated(
      padding: const EdgeInsets.all(16),
      itemCount: data.length,
      separatorBuilder: (context, index) => const Divider(),
      itemBuilder: (context, index) {
        return ListTile(
          leading: Container(
            padding: const EdgeInsets.all(8),
            decoration: BoxDecoration(
              color: Colors.red.withOpacity(0.1),
              shape: BoxShape.circle,
            ),
            child: const Icon(Icons.notifications, color: Color(0xFF990000)),
          ),
          title: Text(data[index]['title']!, style: const TextStyle(fontWeight: FontWeight.bold)),
          subtitle: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(data[index]['desc']!),
              const SizedBox(height: 5),
              Text(data[index]['time']!, style: const TextStyle(fontSize: 11, color: Colors.grey)),
            ],
          ),
        );
      },
    );
  }
}
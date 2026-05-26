import 'package:flutter/material.dart';
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:intl/intl.dart'; 
import 'pendaftaran_kelas_page.dart';

class KelasPage extends StatefulWidget {
  final String token;
  final Map userData;

  const KelasPage({super.key, required this.token, required this.userData});

  @override
  State<KelasPage> createState() => _KelasPageState();
}

class _KelasPageState extends State<KelasPage> {
  
  // Fungsi Format Mata Uang Rupiah
  String formatRupiah(String price) {
    try {
      final formatter = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);
      return formatter.format(double.parse(price));
    } catch (e) {
      return "Rp $price";
    }
  }

  // --- SOLUSI GAMBAR 100% BAGUS ---
  // Menangani URL dari API Laravel agar bisa dibaca Emulator (localhost -> 10.0.2.2)
  String formatImageUrl(dynamic p) {
    // Ambil field image_url yang sudah dibuat di API Laravel
    String? url = p['image_url'];
    
    if (url == null || url.isEmpty || url == "null") {
      // Jika image_url null, cek field 'image' (path mentah)
      String? rawPath = p['image'];
      if (rawPath != null && rawPath.isNotEmpty) {
        url = 'http://10.0.2.2:8000/storage/$rawPath';
      } else {
        return "https://via.placeholder.com/600x400?text=Spekta+Academy";
      }
    }
    
    // Ganti localhost ke IP Emulator
    String formattedUrl = url.replaceAll('localhost', '10.0.2.2').replaceAll('127.0.0.1', '10.0.2.2');
    
    // Pastikan path public/ diubah ke storage/ (jika ada kesalahan di backend)
    if (formattedUrl.contains('/public/')) {
      formattedUrl = formattedUrl.replaceAll('/public/', '/storage/');
    }

    return formattedUrl;
  }

  // Fungsi ambil data dari API
  Future<List<dynamic>> getPrograms() async {
    try {
      final response = await http.get(
        Uri.parse('http://10.0.2.2:8000/api/programs'),
        headers: {
          'Authorization': 'Bearer ${widget.token}',
          'Accept': 'application/json',
        },
      );
      if (response.statusCode == 200) {
        return jsonDecode(response.body)['data'];
      } else {
        throw Exception('Gagal memuat data server');
      }
    } catch (e) {
      throw Exception('Kesalahan koneksi: $e');
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      appBar: AppBar(
        title: const Text("Pilihan Kelas", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
        backgroundColor: const Color(0xFF990000), // Merah Spekta
        elevation: 0,
        centerTitle: true,
      ),
      body: FutureBuilder<List<dynamic>>(
        future: getPrograms(),
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return const Center(child: CircularProgressIndicator(color: Color(0xFF990000)));
          }
          if (snapshot.hasError) return const Center(child: Text("Gagal memuat data server. Periksa koneksi API Anda."));
          if (!snapshot.hasData || snapshot.data!.isEmpty) return const Center(child: Text("Kelas belum tersedia."));

          var programs = snapshot.data!;

          return ListView.builder(
            padding: const EdgeInsets.fromLTRB(20, 20, 20, 100),
            physics: const BouncingScrollPhysics(),
            itemCount: programs.length,
            itemBuilder: (context, index) {
              var p = programs[index];
              return _buildPremiumCard(context, p);
            },
          );
        },
      ),
    );
  }

  Widget _buildPremiumCard(BuildContext context, dynamic p) {
    return Container(
      margin: const EdgeInsets.only(bottom: 25),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(30),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.06),
            blurRadius: 20,
            offset: const Offset(0, 10),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // BAGIAN ATAS: GAMBAR + HARGA (STACK)
          Stack(
            children: [
              ClipRRect(
                borderRadius: const BorderRadius.vertical(top: Radius.circular(30)),
                child: SizedBox(
                  height: 200,
                  width: double.infinity,
                  child: Image.network(
                    formatImageUrl(p), // Memanggil fungsi perbaikan URL Gambar
                    fit: BoxFit.cover,
                    loadingBuilder: (context, child, loadingProgress) {
                      if (loadingProgress == null) return child;
                      return Container(
                        color: Colors.grey[100], 
                        child: const Center(child: CircularProgressIndicator(color: Color(0xFF990000)))
                      );
                    },
                    errorBuilder: (context, e, s) => Container(
                      color: Colors.grey[200],
                      child: const Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Icon(Icons.broken_image, size: 50, color: Colors.grey),
                          SizedBox(height: 5),
                          Text("Gambar Tidak Ditemukan", style: TextStyle(fontSize: 10, color: Colors.grey)),
                        ],
                      ),
                    ),
                  ),
                ),
              ),
              // Label Harga
              Positioned(
                bottom: 15,
                right: 15,
                child: Container(
                  padding: const EdgeInsets.symmetric(horizontal: 15, vertical: 8),
                  decoration: BoxDecoration(
                    color: const Color(0xFF990000),
                    borderRadius: BorderRadius.circular(15),
                    boxShadow: [const BoxShadow(color: Colors.black26, blurRadius: 8)],
                  ),
                  child: Text(
                    formatRupiah(p['price'].toString()),
                    style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 13),
                  ),
                ),
              ),
            ],
          ),

          // BAGIAN ISI DATA
          Padding(
            padding: const EdgeInsets.all(20),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  p['title'] ?? "Nama Kelas",
                  style: const TextStyle(fontSize: 19, fontWeight: FontWeight.w900, color: Color(0xFF2D3436)),
                ),
                const SizedBox(height: 8),
                Text(
                  p['description'] ?? "Deskripsi bimbingan profesional Spekta Academy.",
                  maxLines: 2,
                  overflow: TextOverflow.ellipsis,
                  style: TextStyle(color: Colors.grey[600], fontSize: 12, height: 1.5),
                ),
                const SizedBox(height: 25),
                
                // TOMBOL INFO SELENGKAPNYA
                SizedBox(
                  width: double.infinity,
                  child: ElevatedButton(
                    onPressed: () {
                      // Navigasi ke Halaman Biodata/Pendaftaran
                      Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (context) => PendaftaranKelasPage(
                            classId: p['program_id'], // Primary Key dari tabel Admin
                            className: p['title'],
                            token: widget.token,
                            userData: widget.userData,
                          ),
                        ),
                      );
                    },
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFFFFC107), // Kuning Amber
                      foregroundColor: Colors.white,
                      elevation: 0,
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
                      padding: const EdgeInsets.symmetric(vertical: 18),
                    ),
                    child: const Text(
                      "Info Selengkapnya", 
                      style: TextStyle(fontWeight: FontWeight.bold, fontSize: 14, letterSpacing: 1)
                    ),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
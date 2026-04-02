import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:flutter/services.dart'; // Untuk fitur salin nomor rekening
import 'dart:io';
import 'dart:convert';
import 'package:http/http.dart' as http;
import '../services/auth_service.dart';

class PendaftaranKelasPage extends StatefulWidget {
  final int classId;
  final String className;
  final String token;
  final Map userData;

  const PendaftaranKelasPage({
    super.key, 
    required this.classId, 
    required this.className, 
    required this.token, 
    required this.userData
  });

  @override
  State<PendaftaranKelasPage> createState() => _PendaftaranKelasPageState();
}

class _PendaftaranKelasPageState extends State<PendaftaranKelasPage> {
  File? _imageFile;
  final Color spektaRed = const Color(0xFF990000);

  late TextEditingController _nameCtrl;
  late TextEditingController _nisnCtrl;

  // Variabel Promo
  final TextEditingController _promoCtrl = TextEditingController();
  double _originalPrice = 900000; 
  double _discount = 0;
  double _totalToPay = 900000;
  bool _isPromoLoading = false;

  @override
  void initState() {
    super.initState();
    _nameCtrl = TextEditingController(text: widget.userData['name']);
    _nisnCtrl = TextEditingController(text: widget.userData['student']?['nisn'] ?? "-");
  }

  // Fungsi Cek Promo ke API Laravel
  Future<void> _checkPromo() async {
    if (_promoCtrl.text.isEmpty) return;
    setState(() => _isPromoLoading = true);
    try {
      final response = await http.post(
        Uri.parse('http://10.0.2.2:8000/api/admin/promo/check'), // Gunakan IP 10.0.2.2 untuk emulator
        headers: {
          'Authorization': 'Bearer ${widget.token}',
          'Accept': 'application/json',
        },
        body: {
          'kode_promo': _promoCtrl.text,
          'class_id': widget.classId.toString(),
        },
      );

      final data = jsonDecode(response.body);

      if (data['success'] == true) {
        double potong = 0;
        double nilaiDiskon = double.parse(data['nilai'].toString());

        if (data['tipe'] == 'percentage') {
          potong = (_originalPrice * nilaiDiskon) / 100;
        } else {
          potong = nilaiDiskon;
        }

        setState(() {
          _discount = potong;
          _totalToPay = _originalPrice - potong;
        });

        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(backgroundColor: Colors.green, content: Text("Promo Berhasil Digunakan!"))
        );
      } else {
        setState(() { _discount = 0; _totalToPay = _originalPrice; });
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(backgroundColor: Colors.red, content: Text(data['message'] ?? "Kode Promo Tidak Valid"))
        );
      }
    } catch (e) {
      debugPrint("Error: $e");
    } finally {
      setState(() => _isPromoLoading = false);
    }
  }

  Future<void> _pickImage() async {
    final picker = ImagePicker();
    final pickedFile = await picker.pickImage(source: ImageSource.gallery);
    if (pickedFile != null) setState(() => _imageFile = File(pickedFile.path));
  }

  void _submitData() async {
    if (_imageFile == null) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text("Unggah bukti transfer Terlebih Dahulu!")));
      return;
    }
    showDialog(context: context, builder: (_) => const Center(child: CircularProgressIndicator()));

    var streamedResp = await AuthService.joinClass(widget.classId, _imageFile!.path, widget.token);
    var response = await http.Response.fromStream(streamedResp);

    if (!mounted) return;
    Navigator.pop(context); 

    if (response.statusCode == 200) {
      Navigator.pop(context); 
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(backgroundColor: Colors.green, content: Text("Pendaftaran Berhasil!")));
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(title: const Text("Konfirmasi Pembayaran"), backgroundColor: spektaRed, foregroundColor: Colors.white),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(25),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text("Data Pendaftar", style: TextStyle(fontWeight: FontWeight.bold, color: Colors.grey)),
            const SizedBox(height: 10),
            _buildReadOnlyField(_nameCtrl, "Nama Lengkap", Icons.person),
            _buildReadOnlyField(_nisnCtrl, "NISN", Icons.numbers),
            
            const SizedBox(height: 20),

            const Text("Gunakan Kode Promo", style: TextStyle(fontWeight: FontWeight.bold, color: Colors.grey)),
            const SizedBox(height: 10),
            Row(
              children: [
                Expanded(
                  child: TextField(
                    controller: _promoCtrl,
                    decoration: InputDecoration(
                      hintText: "Contoh: SPECTA50",
                      filled: true, fillColor: Colors.white,
                      border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                    ),
                  ),
                ),
                const SizedBox(width: 10),
                ElevatedButton(
                  style: ElevatedButton.styleFrom(backgroundColor: Colors.black, padding: const EdgeInsets.symmetric(vertical: 18, horizontal: 20)),
                  onPressed: _isPromoLoading ? null : _checkPromo,
                  child: _isPromoLoading 
                      ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(color: Colors.white)) 
                      : const Text("CEK", style: TextStyle(color: Colors.white)),
                )
              ],
            ),

            const SizedBox(height: 30),

            // KOTAK RINCIAN PEMBAYARAN
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(color: Colors.red[50], borderRadius: BorderRadius.circular(15), border: Border.all(color: Colors.red.shade200)),
              child: Column(
                children: [
                  const Text("Transfer ke Rekening Mandiri:", style: TextStyle(fontSize: 12)),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      const Text("123-456-7890", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 18, color: Color(0xFF990000))),
                      IconButton(
                        icon: const Icon(Icons.copy, size: 18),
                        onPressed: () {
                          Clipboard.setData(const ClipboardData(text: "1234567890"));
                          ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text("Nomor disalin")));
                        },
                      )
                    ],
                  ),
                  const Divider(height: 30),
                  
                  // PERBAIKAN: mainAxisAlignment: MainAxisAlignment.spaceBetween
                  _buildPriceRow("Harga Program", "Rp ${_originalPrice.toInt()}"),
                  if (_discount > 0) 
                    _buildPriceRow("Potongan Promo", "- Rp ${_discount.toInt()}", textColor: Colors.green),
                  
                  const SizedBox(height: 10),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween, // <--- SUDAH DIPERBAIKI
                    children: [
                      const Text("TOTAL BAYAR:", style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold)),
                      Text("Rp ${_totalToPay.toInt()}", style: TextStyle(fontSize: 22, fontWeight: FontWeight.w900, color: spektaRed)),
                    ],
                  )
                ],
              ),
            ),

            const SizedBox(height: 25),
            const Text("Upload Bukti Transfer", style: TextStyle(fontWeight: FontWeight.bold, color: Colors.grey)),
            const SizedBox(height: 10),
            InkWell(
              onTap: _pickImage,
              child: Container(
                height: 150, width: double.infinity,
                decoration: BoxDecoration(color: Colors.grey[100], borderRadius: BorderRadius.circular(15), border: Border.all(color: Colors.grey.shade300)),
                child: _imageFile == null 
                  ? const Icon(Icons.add_a_photo_outlined, size: 50, color: Colors.grey)
                  : ClipRRect(borderRadius: BorderRadius.circular(15), child: Image.file(_imageFile!, fit: BoxFit.cover)),
              ),
            ),

            const SizedBox(height: 40),
            ElevatedButton(
              style: ElevatedButton.styleFrom(backgroundColor: spektaRed, minimumSize: const Size(double.infinity, 60), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15))),
              onPressed: _submitData,
              child: const Text("KONFIRMASI PEMBAYARAN", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
            ),
          ],
        ),
      ),
    );
  }

  // WIDGET HELPER RINCIAN HARGA
  Widget _buildPriceRow(String label, String value, {Color textColor = Colors.black}) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 2),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween, // <--- SUDAH DIPERBAIKI
        children: [
          Text(label, style: const TextStyle(fontSize: 12, color: Colors.grey)),
          Text(value, style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: textColor)),
        ],
      ),
    );
  }

  Widget _buildReadOnlyField(TextEditingController ctrl, String label, IconData icon) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 15),
      child: TextField(
        controller: ctrl,
        readOnly: true,
        decoration: InputDecoration(labelText: label, prefixIcon: Icon(icon, color: spektaRed), filled: true, fillColor: Colors.grey[100], border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide.none)),
      ),
    );
  }
}
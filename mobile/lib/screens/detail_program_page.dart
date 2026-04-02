import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:flutter/services.dart'; // UNTUK FITUR SALIN REKENING
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
    super.key, required this.classId, required this.className, required this.token, required this.userData
  });

  @override
  State<PendaftaranKelasPage> createState() => _PendaftaranKelasPageState();
}

class _PendaftaranKelasPageState extends State<PendaftaranKelasPage> {
  File? _imageFile;
  final Color spektaRed = const Color(0xFF990000);

  late TextEditingController _nameCtrl;
  late TextEditingController _nisnCtrl;
  
  // LOGIKA PROMO
  final TextEditingController _promoCtrl = TextEditingController();
  double _price = 900000; 
  double _discount = 0;
  double _totalPay = 900000;
  bool _isChecking = false;

  @override
  void initState() {
    super.initState();
    _nameCtrl = TextEditingController(text: widget.userData['name']);
    _nisnCtrl = TextEditingController(text: widget.userData['student']['nisn'] ?? "-");
  }

  // FUNGSI CEK PROMO
  Future<void> _applyPromo() async {
    if (_promoCtrl.text.isEmpty) return;
    setState(() => _isChecking = true);
    try {
      final res = await http.post(
        Uri.parse('http://10.0.2.2:8000/api/admin/promo/check'), 
        body: {'kode_promo': _promoCtrl.text, 'class_id': widget.classId.toString()},
      );
      final data = jsonDecode(res.body);
      if (data['success']) {
        double val = double.parse(data['nilai'].toString());
        setState(() {
          _discount = (data['tipe'] == 'percentage') ? (_price * val / 100) : val;
          _totalPay = _price - _discount;
        });
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(backgroundColor: Colors.green, content: Text("Promo Berhasil!")));
      } else {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(backgroundColor: Colors.red, content: Text("Kode Tidak Valid")));
      }
    } finally {
      setState(() => _isChecking = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text("Pembayaran"), backgroundColor: spektaRed, foregroundColor: Colors.white),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // STEP 1: RINCIAN TAGIHAN
            _sectionTitle("Rincian Pendaftaran"),
            _buildInfoCard(child: Column(children: [
              _rowPrice("Program", widget.className),
              _rowPrice("Harga Normal", "Rp 900.000"),
              if (_discount > 0) _rowPrice("Potongan Promo", "- Rp ${_discount.toInt()}", color: Colors.green),
              const Divider(),
              _rowPrice("TOTAL BAYAR", "Rp ${_totalPay.toInt()}", isBold: true, color: spektaRed, size: 18),
            ])),

            const SizedBox(height: 20),

            // STEP 2: INPUT PROMO
            _sectionTitle("Gunakan Kode Promo"),
            Row(children: [
              Expanded(child: TextField(controller: _promoCtrl, decoration: const InputDecoration(hintText: "Masukkan Kode", border: OutlineInputBorder()))),
              const SizedBox(width: 10),
              ElevatedButton(onPressed: _isChecking ? null : _applyPromo, style: ElevatedButton.styleFrom(backgroundColor: Colors.black), child: const Text("CEK", style: TextStyle(color: Colors.white)))
            ]),

            const SizedBox(height: 25),

            // STEP 3: INSTRUKSI BANK
            _sectionTitle("Metode Pembayaran"),
            _buildInfoCard(color: Colors.red[50]!, child: Column(children: [
              const Text("Transfer ke Rekening Mandiri:", style: TextStyle(fontSize: 12)),
              Row(mainAxisAlignment: MainAxisAlignment.center, children: [
                const Text("123-456-7890", style: TextStyle(fontSize: 22, fontWeight: FontWeight.bold)),
                IconButton(icon: const Icon(Icons.copy, size: 20), onPressed: () {
                  Clipboard.setData(const ClipboardData(text: "1234567890"));
                  ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text("Rekening disalin!")));
                })
              ]),
              const Text("a/n Spekta Academy Indonesia", style: TextStyle(fontWeight: FontWeight.bold)),
            ])),

            const SizedBox(height: 25),

            // STEP 4: UPLOAD
            _sectionTitle("Bukti Transfer"),
            InkWell(
              onTap: () async {
                final file = await ImagePicker().pickImage(source: ImageSource.gallery);
                if (file != null) setState(() => _imageFile = File(file.path));
              },
              child: Container(
                height: 150, width: double.infinity,
                decoration: BoxDecoration(color: Colors.grey[100], borderRadius: BorderRadius.circular(10), border: Border.all(color: Colors.grey[300]!)),
                child: _imageFile == null ? const Icon(Icons.camera_enhance_outlined, size: 40, color: Colors.grey) : Image.file(_imageFile!, fit: BoxFit.cover),
              ),
            ),

            const SizedBox(height: 30),

            ElevatedButton(
              onPressed: () { /* Fungsi Submit */ },
              style: ElevatedButton.styleFrom(backgroundColor: spektaRed, minimumSize: const Size(double.infinity, 55)),
              child: const Text("KONFIRMASI SEKARANG", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
            )
          ],
        ),
      ),
    );
  }

  // WIDGET HELPER
  Widget _sectionTitle(String text) => Padding(padding: const EdgeInsets.only(bottom: 10), child: Text(text, style: const TextStyle(fontWeight: FontWeight.bold, color: Colors.grey)));
  
  Widget _buildInfoCard({required Widget child, Color color = Colors.white}) => Container(
    padding: const EdgeInsets.all(15),
    width: double.infinity,
    decoration: BoxDecoration(color: color, borderRadius: BorderRadius.circular(12), border: Border.all(color: Colors.grey[200]!), shadow: [BoxShadow(color: Colors.black.withOpacity(0.02), blurRadius: 10)]),
    child: child,
  );

  Widget _rowPrice(String label, String val, {bool isBold = false, Color color = Colors.black, double size = 13}) => Padding(
    padding: const EdgeInsets.symmetric(vertical: 4),
    child: Row(mainAxisAlignment: MainAxisAlignment.between, children: [
      Text(label, style: const TextStyle(fontSize: 12)),
      Text(val, style: TextStyle(fontWeight: isBold ? FontWeight.bold : FontWeight.normal, color: color, fontSize: size)),
    ]),
  );
}
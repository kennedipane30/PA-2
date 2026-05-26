import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:midtrans_sdk/midtrans_sdk.dart'; // Library Midtrans

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
final Color spektaRed = const Color(0xFF990000);
MidtransSDK? _midtrans;
String? _currentOrderId;

// LOGIKA HARGA & PROMO
final TextEditingController _promoCtrl = TextEditingController();
double _price = 900000;
double _discount = 0;
double _totalPay = 900000;
bool _isChecking = false;
bool _isPaymentLoading = false;

@override
void initState() {
super.initState();
_initMidtrans(); // Inisialisasi Midtrans saat halaman dibuka
}

// 1. INISIALISASI MIDTRANS SDK
void _initMidtrans() async {
_midtrans = await MidtransSDK.init(
config: MidtransConfig(
clientKey: "Mid-client-TegOK5U1O7Heu0tr", // Client Key Sandbox Anda
merchantBaseUrl: "http://10.0.2.2:8000/api/", // URL API Laravel
colorTheme: ColorTheme(
colorPrimary: spektaRed,
colorPrimaryDark: spektaRed,
colorSecondary: spektaRed,
),
),
);


_midtrans?.setTransactionFinishedCallback((result) {
  if (result.status == 'settlement' || result.status == 'capture') {
    _confirmPayment();
  } else if (result.status == 'cancel') {
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(content: Text("Pembayaran Dibatalkan"))
    );
  }
});

}

// 2. FUNGSI CEK PROMO
Future<void> _applyPromo() async {
if (_promoCtrl.text.isEmpty) return;
setState(() => _isChecking = true);
try {
final res = await http.post(
Uri.parse('http://10.0.2.2:8000/api/admin/promo/check'),
headers: {
'Authorization': 'Bearer ${widget.token}',
'Accept': 'application/json',
},
body: {
'kode_promo': _promoCtrl.text,
'class_id': widget.classId.toString()
},
);
final data = jsonDecode(res.body);
if (data['success'] == true) {
double val = double.parse(data['nilai'].toString());
setState(() {
_discount = (data['tipe'] == 'percentage') ? (_price * val / 100) : val;
_totalPay = _price - _discount;
});
ScaffoldMessenger.of(context).showSnackBar(const SnackBar(backgroundColor: Colors.green, content: Text("Promo Berhasil!")));
} else {
ScaffoldMessenger.of(context).showSnackBar(const SnackBar(backgroundColor: Colors.red, content: Text("Kode Tidak Valid")));
}
} catch (e) {
debugPrint("Error Promo: $e");
} finally {
setState(() => _isChecking = false);
}
}

// 3. FUNGSI UTAMA: BAYAR DENGAN MIDTRANS
Future<void> _payWithMidtrans() async {
setState(() => _isPaymentLoading = true);
try {
// Minta token dari Laravel
final response = await http.post(
Uri.parse('http://10.0.2.2:8000/api/midtrans/token'),
headers: {
'Content-Type': 'application/json',
'Authorization': 'Bearer ${widget.token}',
},
body: jsonEncode({
"nama_lengkap": widget.userData['name'],
"total_bayar": _totalPay.toInt(),
"class_id": widget.classId,
}),
);


final data = jsonDecode(response.body);

  if (response.statusCode == 200 && data['status'] == 'success') {
    String snapToken = data['token'];
    _currentOrderId = data['order_id'];
    // MEMBUKA JENDELA MIDTRANS (Bank/QRIS Otomatis)
    _midtrans?.startPaymentUiFlow(token: snapToken);
  } else {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(backgroundColor: Colors.red, content: Text(data['message'] ?? "Gagal mengambil token"))
    );
  }
} catch (e) {
  print("Error: $e");
  ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text("Masalah koneksi ke server")));
} finally {
  setState(() => _isPaymentLoading = false);
}

}

Future<void> _confirmPayment() async {
  if (_currentOrderId == null) return;

  try {
    final response = await http.post(
      Uri.parse('http://10.0.2.2:8000/api/midtrans/verify'),
      headers: {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer ${widget.token}',
      },
      body: jsonEncode({
        'order_id': _currentOrderId,
      }),
    );

    final data = jsonDecode(response.body);
    if (response.statusCode == 200 && data['status'] == 'success') {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(backgroundColor: Colors.green, content: Text("Pembayaran berhasil diverifikasi & disimpan"))
      );
      if (Navigator.canPop(context)) Navigator.pop(context);
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(backgroundColor: Colors.red, content: Text(data['message'] ?? "Gagal verifikasi pembayaran"))
      );
    }
  } catch (e) {
    debugPrint("Verifikasi pembayaran gagal: $e");
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(backgroundColor: Colors.red, content: Text("Gagal menghubungi server untuk verifikasi"))
    );
  }
}

@override
Widget build(BuildContext context) {
return Scaffold(
backgroundColor: Colors.white,
appBar: AppBar(
title: const Text("Konfirmasi Pembayaran"),
backgroundColor: spektaRed,
foregroundColor: Colors.white,
elevation: 0
),
body: SingleChildScrollView(
padding: const EdgeInsets.all(25),
child: Column(
crossAxisAlignment: CrossAxisAlignment.start,
children: [
_sectionTitle("Rincian Pendaftaran"),
_buildInfoCard(child: Column(children: [
_rowPrice("Program", widget.className),
_rowPrice("Harga Normal", "Rp ${_price.toInt()}"),
if (_discount > 0) _rowPrice("Potongan Promo", "- Rp ${_discount.toInt()}", color: Colors.green),
const Divider(height: 30),
_rowPrice("TOTAL BAYAR", "Rp ${_totalPay.toInt()}", isBold: true, color: spektaRed, size: 22),
])),


const SizedBox(height: 25),

        _sectionTitle("Gunakan Kode Promo"),
        Row(children: [
          Expanded(
            child: TextField(
              controller: _promoCtrl, 
              decoration: InputDecoration(hintText: "Masukkan Kode", border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)))
            )
          ),
          const SizedBox(width: 10),
          ElevatedButton(
            onPressed: _isChecking ? null : _applyPromo, 
            style: ElevatedButton.styleFrom(backgroundColor: Colors.black, padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 15)), 
            child: _isChecking 
              ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2)) 
              : const Text("CEK", style: TextStyle(color: Colors.white))
          )
        ]),

        const SizedBox(height: 50),

        // TOMBOL BAYAR SEKARANG (TERHUBUNG KE MIDTRANS)
        ElevatedButton(
          onPressed: _isPaymentLoading ? null : _payWithMidtrans,
          style: ElevatedButton.styleFrom(
            backgroundColor: spektaRed, 
            minimumSize: const Size(double.infinity, 65),
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15))
          ),
          child: _isPaymentLoading 
            ? const CircularProgressIndicator(color: Colors.white)
            : const Text("BAYAR SEKARANG", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 18)),
        ),
        
        const SizedBox(height: 20),
        const Center(child: Text("*Pilih metode bank atau QRIS setelah klik tombol di atas", style: TextStyle(fontSize: 10, color: Colors.grey))),
      ],
    ),
  ),
);

}

// WIDGET HELPER
Widget _sectionTitle(String text) => Padding(padding: const EdgeInsets.only(bottom: 10), child: Text(text, style: const TextStyle(fontWeight: FontWeight.bold, color: Colors.grey)));

Widget _buildInfoCard({required Widget child, Color color = Colors.white}) => Container(
padding: const EdgeInsets.all(20),
width: double.infinity,
decoration: BoxDecoration(color: color, borderRadius: BorderRadius.circular(15), border: Border.all(color: Colors.red.shade100)),
child: child,
);

Widget _rowPrice(String label, String val, {bool isBold = false, Color color = Colors.black, double size = 13}) => Padding(
padding: const EdgeInsets.symmetric(vertical: 4),
child: Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
Text(label, style: const TextStyle(fontSize: 13, color: Colors.black54)),
Text(val, style: TextStyle(fontWeight: isBold ? FontWeight.bold : FontWeight.normal, color: color, fontSize: size)),
]),
);
}
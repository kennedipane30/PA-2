import 'package:flutter/material.dart';
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:midtrans_sdk/midtrans_sdk.dart';
import 'package:intl/intl.dart';

// ==========================================================
// 1. HALAMAN BUKTI PEMBAYARAN / STRUK
// ==========================================================
class PaymentSuccessPage extends StatelessWidget {
  final String className;
  final String orderId;
  final int totalPaid;

  const PaymentSuccessPage({
    super.key, 
    required this.className, 
    required this.orderId, 
    required this.totalPaid
  });

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey[100],
      body: Center(
        child: SingleChildScrollView(
          child: Padding(
            padding: const EdgeInsets.all(25.0),
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                const Icon(Icons.check_circle_rounded, color: Colors.green, size: 90),
                const SizedBox(height: 20),
                const Text("PEMBAYARAN BERHASIL!", 
                  style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold, letterSpacing: 1.2)),
                const SizedBox(height: 30),

                Container(
                  padding: const EdgeInsets.all(25),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(20),
                    boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 10, offset: const Offset(0, 5))]
                  ),
                  child: Column(
                    children: [
                      const Text("BUKTI PENDAFTARAN RESMI", 
                        style: TextStyle(fontWeight: FontWeight.bold, color: Colors.grey, fontSize: 11)),
                      const Divider(height: 30, thickness: 1),
                      _rowStruk("ID Transaksi", orderId),
                      _rowStruk("Program", className),
                      _rowStruk("Tanggal", DateFormat('dd MMM yyyy, HH:mm').format(DateTime.now())),
                      _rowStruk("Status", "VERIFIED / LUNAS", color: Colors.green, isBold: true),
                      const Divider(height: 40, thickness: 1),
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          const Text("TOTAL BAYAR", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15)),
                          Text(
                            NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0).format(totalPaid),
                            style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 18, color: Color(0xFF990000)),
                          ),
                        ],
                      ),
                    ],
                  ),
                ),

                const SizedBox(height: 40),
                ElevatedButton(
                  onPressed: () => Navigator.popUntil(context, (route) => route.isFirst),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFF990000),
                    minimumSize: const Size(double.infinity, 60),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15))
                  ),
                  child: const Text("MULAI BELAJAR SEKARANG", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _rowStruk(String label, String val, {Color color = Colors.black, bool isBold = false}) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 8),
      child: Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
        Text(label, style: const TextStyle(color: Colors.black54, fontSize: 12)),
        Text(val, style: TextStyle(color: color, fontWeight: isBold ? FontWeight.bold : FontWeight.normal, fontSize: 12)),
      ]),
    );
  }
}

// ==========================================================
// 2. HALAMAN UTAMA PENDAFTARAN
// ==========================================================
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
  bool _isMidtransReady = false;
  String? _currentOrderId;
  
  // URL NGROK (Pastikan Update sesuai terminal Ngrok Anda)
  final String baseUrl = "https://7b1e-114-10-85-152.ngrok-free.app/api";

  final TextEditingController _promoCtrl = TextEditingController();
  double _price = 0; 
  double _discount = 0;
  double _totalPay = 0;
  String _dynamicClassName = "";
  
  bool _isLoadingData = true;
  bool _isChecking = false;
  bool _isPaymentLoading = false;

  @override
  void initState() {
    super.initState();
    _fetchClassDetails();
    _initMidtrans();
  }

  Future<void> _fetchClassDetails() async {
    try {
      final res = await http.get(
        Uri.parse('$baseUrl/programs/${widget.classId}'),
        headers: {'Authorization': 'Bearer ${widget.token}', 'Accept': 'application/json'},
      );
      if (res.statusCode == 200) {
        final data = jsonDecode(res.body)['data'];
        setState(() {
          _dynamicClassName = data['title'];
          _price = double.parse(data['price'].toString());
          if (data['is_promo'] == true || data['is_promo'] == 1) {
            _discount = _price - double.parse(data['harga_promo'].toString());
          }
          _totalPay = _price - _discount;
          _isLoadingData = false;
        });
      }
    } catch (e) {
      if(mounted) setState(() => _isLoadingData = false);
    }
  }

  void _initMidtrans() async {
    try {
      final midtrans = await MidtransSDK.init(
        config: MidtransConfig(
          clientKey: "Mid-client-TegOK5U1O7Heu0tr", 
          merchantBaseUrl: "$baseUrl/",
          colorTheme: ColorTheme(colorPrimary: spektaRed, colorPrimaryDark: spektaRed, colorSecondary: spektaRed),
        ),
      );
      setState(() { _midtrans = midtrans; _isMidtransReady = true; });

      _midtrans?.setTransactionFinishedCallback((result) {
        // Logika Retry: Saat jendela tutup, kita mulai verifikasi berulang
        if (result.status == 'settlement' || result.status == 'capture' || result.status == 'pending') {
          _verifyPaymentWithRetry(); 
        } else {
          if (mounted) setState(() => _isPaymentLoading = false);
        }
      });
    } catch (e) {
      debugPrint("Midtrans Init Error: $e");
    }
  }

  void _goToSuccessPage() {
    Navigator.pushReplacement(
      context, 
      MaterialPageRoute(
        builder: (context) => PaymentSuccessPage(
          className: _dynamicClassName,
          orderId: _currentOrderId ?? "INV-${DateTime.now().millisecondsSinceEpoch}",
          totalPaid: _totalPay.toInt(),
        )
      )
    );
  }

  // LOGIKA RETRY (CEK 5 KALI DENGAN JEDA 2 DETIK)
  Future<void> _verifyPaymentWithRetry() async {
    int maxRetries = 5;
    int currentRetry = 0;

    if (mounted) setState(() => _isPaymentLoading = true);

    while (currentRetry < maxRetries) {
      currentRetry++;
      debugPrint("Verifikasi ke-$currentRetry...");

      try {
        final res = await http.post(
          Uri.parse('$baseUrl/midtrans/check-status'),
          headers: {'Content-Type': 'application/json', 'Authorization': 'Bearer ${widget.token}'},
          body: jsonEncode({
            'user_id': widget.userData['id'] ?? widget.userData['user_id'],
            'program_id': widget.classId,
          }),
        );

        final data = jsonDecode(res.body);
        debugPrint("Respon: ${res.body}");

        if (data['has_access'] == true) {
          _goToSuccessPage();
          return; // Berhasil, keluar dari fungsi
        }
      } catch (e) {
        debugPrint("Error: $e");
      }

      // Tunggu 2 detik sebelum cek lagi (memberi waktu Ngrok)
      await Future.delayed(const Duration(seconds: 2));
    }

    // Jika sampai akhir tetap gagal
    if (mounted) {
      setState(() => _isPaymentLoading = false);
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text("Pembayaran diproses. Silakan cek menu profil Anda.")),
      );
    }
  }

  Future<void> _applyPromo() async {
    if (_promoCtrl.text.isEmpty) return;
    setState(() => _isChecking = true);
    try {
      final res = await http.post(
        Uri.parse('$baseUrl/admin/promo/check'), 
        headers: {'Authorization': 'Bearer ${widget.token}', 'Accept': 'application/json'},
        body: {'kode_promo': _promoCtrl.text, 'class_id': widget.classId.toString()},
      );
      final data = jsonDecode(res.body);
      if (data['success']) {
        double val = double.parse(data['nilai'].toString());
        setState(() {
          _discount = (data['tipe'] == 'percentage') ? (_price * val / 100) : val;
          _totalPay = _price - _discount;
        });
      }
    } finally {
      if(mounted) setState(() => _isChecking = false);
    }
  }

  Future<void> _payNow() async {
    if (!_isMidtransReady) return;
    setState(() => _isPaymentLoading = true);

    try {
      final response = await http.post(
        Uri.parse('$baseUrl/midtrans/token'),
        headers: {'Content-Type': 'application/json', 'Authorization': 'Bearer ${widget.token}'},
        body: jsonEncode({
          "user_id": widget.userData['id'] ?? widget.userData['user_id'],
          "program_id": widget.classId,
          "name": widget.userData['name'],
          "email": widget.userData['email'] ?? "siswa@spekta.com",
          "harga_asli": _price.toInt(),
          "diskon": _discount.toInt(),
          "total_bayar": _totalPay.toInt(),
        }),
      );

      final data = jsonDecode(response.body);

      if (response.statusCode == 200 && data['status'] == 'success') {
        _currentOrderId = data['order_id'];
        await _midtrans?.startPaymentUiFlow(token: data['token']);
        
        // JANGAN panggil verify di sini, biarkan callback SDK yang memicu
      } else {
        setState(() => _isPaymentLoading = false);
      }
    } catch (e) {
      if (mounted) setState(() => _isPaymentLoading = false);
    }
  }

  String formatRupiah(double value) => NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0).format(value);

  @override
  Widget build(BuildContext context) {
    if (_isLoadingData) return const Scaffold(body: Center(child: CircularProgressIndicator()));
    
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(title: const Text("Konfirmasi Pembayaran"), backgroundColor: spektaRed, foregroundColor: Colors.white, elevation: 0),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(25),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            _sectionTitle("Rincian Pendaftaran"),
            _buildInfoCard(child: Column(children: [
              _rowPrice("Program", _dynamicClassName),
              _rowPrice("Harga Normal", formatRupiah(_price)),
              if (_discount > 0) _rowPrice("Total Potongan", "- ${formatRupiah(_discount)}", color: Colors.green),
              const Divider(height: 30),
              _rowPrice("TOTAL BAYAR", formatRupiah(_totalPay), isBold: true, color: spektaRed, size: 22),
            ])),
            const SizedBox(height: 25),
            _sectionTitle("Gunakan Kode Promo"),
            Row(children: [
              Expanded(child: TextField(controller: _promoCtrl, decoration: InputDecoration(hintText: "Masukkan Kode", border: OutlineInputBorder(borderRadius: BorderRadius.circular(12))))),
              const SizedBox(width: 10),
              ElevatedButton(
                onPressed: _isChecking ? null : _applyPromo, 
                style: ElevatedButton.styleFrom(backgroundColor: Colors.black, padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 15)), 
                child: _isChecking ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2)) : const Text("CEK", style: TextStyle(color: Colors.white))
              )
            ]),
            const SizedBox(height: 40),
            ElevatedButton(
              onPressed: (!_isMidtransReady || _isPaymentLoading) ? null : _payNow,
              style: ElevatedButton.styleFrom(backgroundColor: spektaRed, minimumSize: const Size(double.infinity, 65), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15))),
              child: _isPaymentLoading 
                ? const CircularProgressIndicator(color: Colors.white)
                : const Text("BAYAR SEKARANG", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)),
            ),
          ],
        ),
      ),
    );
  }

  Widget _sectionTitle(String text) => Padding(padding: const EdgeInsets.only(bottom: 10), child: Text(text, style: const TextStyle(fontWeight: FontWeight.bold, color: Colors.grey)));
  Widget _buildInfoCard({required Widget child}) => Container(padding: const EdgeInsets.all(20), decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(15), border: Border.all(color: Colors.red.shade100)), child: child);
  Widget _rowPrice(String label, String val, {bool isBold = false, Color color = Colors.black, double size = 13}) => Padding(
    padding: const EdgeInsets.symmetric(vertical: 4),
    child: Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
      Text(label, style: const TextStyle(fontSize: 12, color: Colors.black54)),
      Text(val, style: TextStyle(fontWeight: isBold ? FontWeight.bold : FontWeight.normal, color: color, fontSize: size)),
    ]),
  );
}
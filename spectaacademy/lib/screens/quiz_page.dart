import 'package:flutter/material.dart';
import '../services/auth_service.dart';
import 'dart:convert';

class QuizPage extends StatefulWidget {
  final List questions;
  final int tryoutId;
  final String token;

  const QuizPage({
    super.key, 
    required this.questions, 
    required this.tryoutId, 
    required this.token
  });

  @override
  State<QuizPage> createState() => _QuizPageState();
}

class _QuizPageState extends State<QuizPage> {
  int _currentIndex = 0;
  Map<int, String> _myAnswers = {}; // Menyimpan {questionsID: "A"}
  final Color spektaRed = const Color(0xFF990000);

  void _nextSoal() {
    if (_currentIndex < widget.questions.length - 1) {
      setState(() => _currentIndex++);
    }
  }

  void _prevSoal() {
    if (_currentIndex > 0) {
      setState(() => _currentIndex--);
    }
  }

  // --- FUNGSI SUBMIT & HITUNG NILAI (Integrasi API) ---
  void _submitQuiz() async {
    // Tampilkan Loading
    showDialog(context: context, barrierDismissible: false, builder: (_) => const Center(child: CircularProgressIndicator(color: Color(0xFF990000))));

    try {
      // Kirim jawaban ke Laravel
      var resp = await AuthService.submitTryout(
        tryoutId: widget.tryoutId, 
        answers: _myAnswers, 
        token: widget.token
      );

      if (!mounted) return;
      Navigator.pop(context); // Tutup Loading

      if (resp.statusCode == 200) {
        final resultData = jsonDecode(resp.body);
        
        // Tampilkan Hasil (Syarat Matakuliah: Real-time Feedback)
        _showResultDialog(resultData['score'].toString(), resultData['resultID'].toString());
      }
    } catch (e) {
      Navigator.pop(context);
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text("Terjadi kesalahan saat mengirim jawaban")));
    }
  }

  // MODUL TAMPILAN NILAI SETELAH FINISH
  void _showResultDialog(String score, String resultId) {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        title: const Text("Ujian Selesai! 🎓", textAlign: TextAlign.center, style: TextStyle(fontWeight: FontWeight.bold)),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            const Text("Nilai Akhir Anda:"),
            const SizedBox(height: 10),
            Text(score, style: TextStyle(fontSize: 48, fontWeight: FontWeight.bold, color: spektaRed)),
            const SizedBox(height: 20),
            const Text("Hebat! Teruslah berlatih bersama Spekta Academy.", textAlign: TextAlign.center),
          ],
        ),
        actions: [
          ElevatedButton(
            style: ElevatedButton.styleFrom(backgroundColor: Colors.green),
            onPressed: () {
              // Nanti sambungkan ke fungsi Download PDF
              ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text("Membuka PDF Pembahasan...")));
            }, 
            child: const Text("DOWNLOAD PEMBAHASAN", style: TextStyle(color: Colors.white))
          ),
          TextButton(
            onPressed: () => Navigator.pop(context), // Kembali ke Detail Kelas
            child: const Text("KEMBALI KE KELAS", style: TextStyle(color: Colors.grey))
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    var q = widget.questions[_currentIndex];

    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: Text("Soal ${_currentIndex + 1} / ${widget.questions.length}"),
        backgroundColor: spektaRed, 
        foregroundColor: Colors.white,
      ),
      body: Column(
        children: [
          // PROGRESS BAR
          LinearProgressIndicator(
            value: (_currentIndex + 1) / widget.questions.length, 
            backgroundColor: Colors.red[50], 
            color: spektaRed
          ),
          
          Expanded(
            child: SingleChildScrollView(
              padding: const EdgeInsets.all(25),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(q['question'], style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w600)),
                  const SizedBox(height: 30),
                  // OPSI JAWABAN (Dinamis dari Database)
                  _buildOption("A", q['option_a'], q['questionsID']),
                  _buildOption("B", q['option_b'], q['questionsID']),
                  _buildOption("C", q['option_c'], q['questionsID']),
                  _buildOption("D", q['option_d'], q['questionsID']),
                ],
              ),
            ),
          ),

          // NAVIGASI BAWAH
          Container(
            padding: const EdgeInsets.all(20),
            decoration: BoxDecoration(
              color: Colors.white, 
              boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 10, offset: const Offset(0, -5))]
            ),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                if (_currentIndex > 0)
                  OutlinedButton(
                    onPressed: _prevSoal, 
                    style: OutlinedButton.styleFrom(side: BorderSide(color: spektaRed)),
                    child: Text("KEMBALI", style: TextStyle(color: spektaRed))
                  )
                else
                  const SizedBox(),
                
                ElevatedButton(
                  style: ElevatedButton.styleFrom(
                    backgroundColor: _currentIndex == widget.questions.length - 1 ? Colors.green : spektaRed,
                    minimumSize: const Size(120, 45)
                  ),
                  onPressed: () {
                    if (_currentIndex == widget.questions.length - 1) {
                      _submitQuiz(); // Klik Finish -> Hitung Skor
                    } else {
                      _nextSoal();
                    }
                  },
                  child: Text(
                    _currentIndex == widget.questions.length - 1 ? "FINISH" : "SELANJUTNYA",
                    style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold),
                  ),
                ),
              ],
            ),
          )
        ],
      ),
    );
  }

  Widget _buildOption(String code, String text, int qId) {
    bool isSelected = _myAnswers[qId] == code;
    
    return GestureDetector(
      onTap: () => setState(() => _myAnswers[qId] = code),
      child: Container(
        margin: const EdgeInsets.only(bottom: 15), 
        padding: const EdgeInsets.all(15),
        decoration: BoxDecoration(
          color: isSelected ? const Color(0xFF990000).withOpacity(0.1) : Colors.white,
          borderRadius: BorderRadius.circular(15),
          border: Border.all(
            color: isSelected ? const Color(0xFF990000) : Colors.grey[300]!, 
            width: 2,
          ),
        ),
        child: Row(
          children: [
            CircleAvatar(
              backgroundColor: isSelected ? const Color(0xFF990000) : Colors.grey[200],
              child: Text(
                code, 
                style: TextStyle(color: isSelected ? Colors.white : Colors.black)
              ),
            ),
            const SizedBox(width: 15),
            Expanded(child: Text(text, style: const TextStyle(fontSize: 16))),
          ],
        ),
      ),
    );
  }
}
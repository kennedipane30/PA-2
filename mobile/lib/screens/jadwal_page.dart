import 'package:flutter/material.dart';
import 'package:table_calendar/table_calendar.dart';
import '../services/auth_service.dart';
import 'dart:convert';

class JadwalPage extends StatefulWidget {
  final String token;
  const JadwalPage({super.key, required this.token});

  @override
  State<JadwalPage> createState() => _JadwalPageState();
}

class _JadwalPageState extends State<JadwalPage> {
  DateTime _focusedDay = DateTime.now();
  DateTime? _selectedDay;
  List _allSchedules = [];
  List _selectedEvents = [];
  bool _isLoading = true;
  
  // Warna Merah Spekta Anda
  final Color spektaRed = const Color(0xFF990000);

  @override
  void initState() {
    super.initState();
    _selectedDay = _focusedDay;
    _fetchJadwal();
  }

  _fetchJadwal() async {
    try {
      var resp = await AuthService.getSiswaSchedule(widget.token);
      if (resp.statusCode == 200) {
        setState(() {
          _allSchedules = jsonDecode(resp.body)['data'];
          _isLoading = false;
          _filterEvents(_selectedDay!);
        });
      }
    } catch (e) {
      debugPrint("Eror ambil jadwal: $e");
      setState(() => _isLoading = false);
    }
  }

  void _filterEvents(DateTime date) {
    String formattedDate =
        "${date.year}-${date.month.toString().padLeft(2, '0')}-${date.day.toString().padLeft(2, '0')}";
    setState(() {
      _selectedEvents =
          _allSchedules.where((s) => s['date'] == formattedDate).toList();
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      // APPBAR SESUAI GAMBAR
      appBar: AppBar(
        title: const Text("Jadwal Belajar",
            style: TextStyle(fontWeight: FontWeight.bold, fontSize: 18)),
        backgroundColor: spektaRed,
        foregroundColor: Colors.white,
        elevation: 0,
        centerTitle: false, // Rapat kiri sesuai gambar
      ),
      body: _isLoading
          ? Center(child: CircularProgressIndicator(color: spektaRed))
          : Column(
              children: [
                // --- BAGIAN ATAS: KALENDER (Warna Putih) ---
                Container(
                  padding: const EdgeInsets.only(bottom: 10),
                  color: Colors.white,
                  child: TableCalendar(
                    firstDay: DateTime.utc(2024, 1, 1),
                    lastDay: DateTime.utc(2030, 12, 31),
                    focusedDay: _focusedDay,
                    headerStyle: const HeaderStyle(
                      formatButtonVisible: false, 
                      titleCentered: true,
                      titleTextStyle: TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
                    ),
                    selectedDayPredicate: (day) => isSameDay(_selectedDay, day),
                    onDaySelected: (selectedDay, focusedDay) {
                      setState(() {
                        _selectedDay = selectedDay;
                        _focusedDay = focusedDay;
                      });
                      _filterEvents(selectedDay);
                    },
                    calendarStyle: CalendarStyle(
                      // Hari yang dipilih: Lingkaran Merah Spekta
                      selectedDecoration: BoxDecoration(
                        color: spektaRed, 
                        shape: BoxShape.circle
                      ),
                      // Hari ini: Lingkaran Merah Muda/Soft
                      todayDecoration: BoxDecoration(
                        color: spektaRed.withOpacity(0.2),
                        shape: BoxShape.circle,
                      ),
                      todayTextStyle: TextStyle(color: spektaRed, fontWeight: FontWeight.bold),
                      markerDecoration: BoxDecoration(color: spektaRed, shape: BoxShape.circle),
                    ),
                    // Loader untuk titik/marker di bawah tanggal
                    eventLoader: (day) {
                      String d = "${day.year}-${day.month.toString().padLeft(2, '0')}-${day.day.toString().padLeft(2, '0')}";
                      return _allSchedules.where((s) => s['date'] == d).toList();
                    },
                  ),
                ),

                // --- BAGIAN BAWAH: KONTENER MERAH MELENGKUNG ---
                Expanded(
                  child: Container(
                    width: double.infinity,
                    padding: const EdgeInsets.only(top: 30, left: 25, right: 25),
                    decoration: BoxDecoration(
                      color: spektaRed,
                      borderRadius: const BorderRadius.only(
                          topLeft: Radius.circular(40),
                          topRight: Radius.circular(40)),
                    ),
                    child: _selectedEvents.isEmpty
                        ? const Center(
                            child: Text(
                              "Tidak ada jadwal hari ini",
                              style: TextStyle(color: Colors.white70, fontSize: 15),
                            ),
                          )
                        : ListView.builder(
                            physics: const BouncingScrollPhysics(),
                            itemCount: _selectedEvents.length,
                            itemBuilder: (context, index) {
                              var item = _selectedEvents[index];
                              return _buildAgendaItem(
                                  item['start_time'] ?? "00:00",
                                  item['end_time'] ?? "00:00",
                                  item['title'] ?? "Materi Belajar",
                                  item['class_model']?['nama_program'] ?? "Program Spekta");
                            },
                          ),
                  ),
                )
              ],
            ),
    );
  }

  // WIDGET ITEM JADWAL (Premium Design didalam Kontener Merah)
  Widget _buildAgendaItem(String start, String end, String title, String className) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 25),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Kolom Jam
          SizedBox(
            width: 50,
            child: Column(
              children: [
                Text(start.substring(0, 5),
                    style: const TextStyle(
                        color: Colors.white,
                        fontSize: 14,
                        fontWeight: FontWeight.bold)),
                Text(end.substring(0, 5),
                    style: const TextStyle(color: Colors.white60, fontSize: 12)),
              ],
            ),
          ),
          const SizedBox(width: 15),
          // Garis Vertikal Pemisah
          Container(width: 1.5, height: 50, color: Colors.white24),
          const SizedBox(width: 15),
          // Deskripsi
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  className.toUpperCase(),
                  style: const TextStyle(
                      color: Colors.yellow,
                      fontSize: 10,
                      fontWeight: FontWeight.bold,
                      letterSpacing: 1.1),
                ),
                const SizedBox(height: 4),
                Text(
                  title,
                  style: const TextStyle(
                      color: Colors.white,
                      fontSize: 16,
                      fontWeight: FontWeight.bold),
                ),
                const Text(
                  "Jangan sampai terlambat ya!",
                  style: TextStyle(
                    color: Colors.white54,
                    fontSize: 10,
                    fontStyle: FontStyle.italic,
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
import 'package:flutter/material.dart';
import 'package:table_calendar/table_calendar.dart';
import '../services/auth_service.dart';
import 'dart:convert';

class JadwalPage extends StatefulWidget {
  final String token;
  const JadwalPage({super.key, required this.token});

  @override State<JadwalPage> createState() => _JadwalPageState();
}

class _JadwalPageState extends State<JadwalPage> {
  DateTime _focusedDay = DateTime.now();
  DateTime? _selectedDay;
  List _allSchedules = [];
  List _selectedEvents = [];
  bool _isLoading = true;
  final Color spektaRed = const Color(0xFF990000);

  @override
  void initState() {
    super.initState();
    _selectedDay = _focusedDay;
    _fetchJadwal();
  }

  _fetchJadwal() async {
    var resp = await AuthService.getSiswaSchedule(widget.token);
    if (resp.statusCode == 200) {
      setState(() {
        _allSchedules = jsonDecode(resp.body)['data'];
        _isLoading = false;
        _filterEvents(_selectedDay!);
      });
    }
  }

  void _filterEvents(DateTime date) {
    String formattedDate = "${date.year}-${date.month.toString().padLeft(2, '0')}-${date.day.toString().padLeft(2, '0')}";
    setState(() {
      _selectedEvents = _allSchedules.where((s) => s['date'] == formattedDate).toList();
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(title: const Text("Jadwal Spekta", style: TextStyle(fontWeight: FontWeight.bold)), backgroundColor: spektaRed, foregroundColor: Colors.white),
      body: _isLoading 
        ? Center(child: CircularProgressIndicator(color: spektaRed))
        : Column(
            children: [
              TableCalendar(
                firstDay: DateTime.utc(2024, 1, 1),
                lastDay: DateTime.utc(2030, 12, 31),
                focusedDay: _focusedDay,
                headerStyle: const HeaderStyle(formatButtonVisible: false, titleCentered: true),
                selectedDayPredicate: (day) => isSameDay(_selectedDay, day),
                onDaySelected: (selectedDay, focusedDay) {
                  setState(() { _selectedDay = selectedDay; _focusedDay = focusedDay; });
                  _filterEvents(selectedDay);
                },
                calendarStyle: CalendarStyle(
                  selectedDecoration: BoxDecoration(color: spektaRed, shape: BoxShape.circle),
                  todayDecoration: BoxDecoration(color: spektaRed.withOpacity(0.3), shape: BoxShape.circle),
                  markerDecoration: BoxDecoration(color: spektaRed, shape: BoxShape.circle),
                ),
                // Logika memunculkan titik jika ada jadwal
                eventLoader: (day) {
                  String d = "${day.year}-${day.month.toString().padLeft(2, '0')}-${day.day.toString().padLeft(2, '0')}";
                  return _allSchedules.where((s) => s['date'] == d).toList();
                },
              ),
              const SizedBox(height: 20),
              Expanded(
                child: Container(
                  width: double.infinity,
                  padding: const EdgeInsets.all(30),
                  decoration: BoxDecoration(
                    color: spektaRed,
                    borderRadius: const BorderRadius.only(topLeft: Radius.circular(50), topRight: Radius.circular(50)),
                  ),
                  child: _selectedEvents.isEmpty 
                    ? const Center(child: Text("Tidak ada jadwal hari ini", style: TextStyle(color: Colors.white70)))
                    : ListView.builder(
                        itemCount: _selectedEvents.length,
                        itemBuilder: (context, index) {
                          var item = _selectedEvents[index];
                          return _buildAgendaItem(item['start_time'], item['end_time'], item['title']);
                        },
                      ),
                ),
              )
            ],
          ),
    );
  }

  Widget _buildAgendaItem(String start, String end, String title) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 20),
      child: Row(
        children: [
          Text("${start.substring(0, 5)}\n${end.substring(0, 5)}", style: const TextStyle(color: Colors.white70, fontSize: 12), textAlign: TextAlign.center),
          const SizedBox(width: 20),
          Container(width: 2, height: 50, color: Colors.white24),
          const SizedBox(width: 20),
          Expanded(child: Text(title, style: const TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold))),
        ],
      ),
    );
  }
}
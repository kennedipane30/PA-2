import 'package:flutter/material.dart';

class KelasPage extends StatelessWidget {
  const KelasPage({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text("Kelas Saya"), backgroundColor: Colors.white),
      body: ListView.builder(
        padding: const EdgeInsets.all(15),
        itemCount: 3,
        itemBuilder: (context, index) {
          return Card(
            child: ListTile(
              leading: const Icon(Icons.menu_book, color: Color(0xFF990000)),
              title: Text("Bimbel Matematika - Kelas ${index + 1}"),
              subtitle: const Text("Pengajar: Pak Guru Spekta"),
              trailing: const Icon(Icons.arrow_forward_ios, size: 15),
            ),
          );
        },
      ),
    );
  }
}
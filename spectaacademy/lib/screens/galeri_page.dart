import 'package:flutter/material.dart';
import 'dart:convert';
import 'package:http/http.dart' as http;

class GaleriPage extends StatelessWidget {
  const GaleriPage({super.key});

  Future<List> getGaleri() async {
    final response = await http.get(Uri.parse('http://10.0.2.2:8000/api/galeri'));
    if (response.statusCode == 200) {
      return jsonDecode(response.body)['data'];
    }
    return [];
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text("Galeri 14 Hari Terakhir"), backgroundColor: const Color(0xFF990000), foregroundColor: Colors.white),
      body: FutureBuilder<List>(
        future: getGaleri(),
        builder: (context, snapshot) {
          if (!snapshot.hasData) return const Center(child: CircularProgressIndicator());
          if (snapshot.data!.isEmpty) return const Center(child: Text("Belum ada foto dalam 14 hari terakhir"));

          return GridView.builder(
            padding: const EdgeInsets.all(15),
            gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(crossAxisCount: 2, crossAxisSpacing: 15, mainAxisSpacing: 15),
            itemCount: snapshot.data!.length,
            itemBuilder: (context, index) {
              var item = snapshot.data![index];
              return Column(
                children: [
                  Expanded(
                    child: ClipRRect(
                      borderRadius: BorderRadius.circular(10),
                      child: Image.network('http://10.0.2.2:8000/storage/${item['foto']}', fit: BoxFit.cover, width: double.infinity),
                    ),
                  ),
                  Text(item['judul'], style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold))
                ],
              );
            },
          );
        },
      ),
    );
  }
}
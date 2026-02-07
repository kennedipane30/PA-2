import 'package:flutter/material.dart';
import 'screens/main_screen.dart';

void main() {
  runApp(const SpektaApp());
}

class SpektaApp extends StatelessWidget {
  const SpektaApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Spekta Academy',
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        // Warna Merah Spekta
        primaryColor: const Color(0xFF990000),
        colorScheme: ColorScheme.fromSeed(seedColor: const Color(0xFF990000)),
        useMaterial3: true,
      ),
      home: const MainScreen(),
    );
  }
}
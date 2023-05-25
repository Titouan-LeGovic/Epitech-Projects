import 'package:flutter/material.dart';
import 'Views/home/homePage.dart';
import 'Views/gallery/galleryPage.dart';
import 'Views/folder/folderPage.dart';
import 'Views/image/imagePage.dart';
import 'Views/profile/profilePage.dart';
import 'Views/settings/settingsPage.dart';

void main() {
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
        title: 'Picts Manager',
        theme: ThemeData(
          primarySwatch: Colors.blue,
        ),
        //home: const HomePage(title: 'PICS MANAGER - Home Page'),
        initialRoute: '/',
        routes: {
          '/': (context) => const HomePage(title: 'PICS MANAGER - Home Page'),
          '/home': (context) =>
              const HomePage(title: 'PICS MANAGER - Home Page'),
          '/gallery': (context) =>
              const GalleryPage(title: 'PICS MANAGER - Gallery Page'),
          '/folder': (context) =>
              const FolderPage(title: 'PICS MANAGER - Folder Page'),
          '/image': (context) =>
              const ImagePage(title: 'PICS MANAGER - Image Page'),
          '/profile': (context) =>
              const ProfilePage(title: 'PICS MANAGER - Profile Page'),
          '/settings': (context) =>
              const SettingsPage(title: 'PICS MANAGER - Settings Page'),
        });
  }
}

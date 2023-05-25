import 'package:flutter/material.dart';
import 'package:http/http.dart';

class HomePage extends StatefulWidget {
  const HomePage({super.key, required this.title});

  final String title;

  @override
  State<HomePage> createState() => _HomePageState();
}

class _HomePageState extends State<HomePage> {
  int _bottomSelectedIndex = 0;

  static const TextStyle optionStyle = TextStyle(fontSize: 30, fontWeight: FontWeight.bold);

  static const List<Widget> _bottomWidgetOptions = <Widget>[
    Text(
      'Index 0: Profile',
      style: optionStyle,
    ),
    Text(
      'Index 1: Folder',
      style: optionStyle,
    ),
    Text(
      'Index 2: Gallery',
      style: optionStyle,
    ),
  ];

  void _onBottomItemTapped(int index) {
    setState(() {
      _bottomSelectedIndex = index;
    });
    switch(_bottomSelectedIndex)
    {
      case 0:
        Navigator.pushNamed(context, '/profile');
        break;
      case 1:
        Navigator.pushNamed(context, '/folder');
        break;
      case 2:
        Navigator.pushNamed(context, '/gallery');
        break;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(widget.title),
      ),
      body: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: <Widget>[
            const Text(
              'You are in the home page:',
            ),
          ]
        ),
      ),
      bottomNavigationBar: BottomNavigationBar(
        items: const <BottomNavigationBarItem>[
          BottomNavigationBarItem(
            icon: Icon(Icons.person),
            label: 'Profile',
            backgroundColor: Colors.blue,
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.folder),
            label: 'Folder',
            backgroundColor: Colors.blue,
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.grid_on),
            label: 'Gallery',
            backgroundColor: Colors.blue,
          ),
        ],
        currentIndex: _bottomSelectedIndex,
        selectedItemColor: Colors.blue[800],
        onTap: _onBottomItemTapped,
      ),
        // ElevatedButton(
        //   onPressed: (){ Navigator.pushNamed(context, '/gallery');},
        //   child: const Icon(Icons.remove),
        // ),
    );
  }
}


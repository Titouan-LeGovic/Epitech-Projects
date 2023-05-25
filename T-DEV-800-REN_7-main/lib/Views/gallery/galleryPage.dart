import 'package:flutter/material.dart';
import 'package:front/Views/gallery/gridBuilder.dart';

class GalleryPage extends StatefulWidget {
  const GalleryPage({super.key, required this.title});

  final String title;

  @override
  State<GalleryPage> createState() => _GalleryPageState();
}

class _GalleryPageState extends State<GalleryPage> {
  bool isSelectionMode = false;
  final int listLength = 30;
  late List<bool> _selected;
  bool _selectAll = false;
  int _bottomSelectedIndex = 2;

  static const TextStyle optionStyle =
      TextStyle(fontSize: 30, fontWeight: FontWeight.bold);

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

  @override
  void initState() {
    super.initState();
    initializeSelection();
  }

  void initializeSelection() {
    _selected = List<bool>.generate(listLength, (_) => false);
  }

  @override
  void dispose() {
    _selected.clear();
    super.dispose();
  }

  void _onBottomItemTapped(int index) {
    setState(() {
      _bottomSelectedIndex = index;
    });
    switch (_bottomSelectedIndex) {
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
        leading: isSelectionMode
            ? IconButton(
                icon: const Icon(Icons.close),
                onPressed: () {
                  setState(() {
                    isSelectionMode = false;
                  });
                  initializeSelection();
                },
              )
            : const SizedBox(),
        actions: <Widget>[
          if (isSelectionMode)
            TextButton(
                child: !_selectAll
                    ? const Text(
                        'select all',
                        style: TextStyle(color: Colors.white),
                      )
                    : const Text(
                        'unselect all',
                        style: TextStyle(color: Colors.white),
                      ),
                onPressed: () {
                  _selectAll = !_selectAll;
                  setState(() {
                    _selected =
                        List<bool>.generate(listLength, (_) => _selectAll);
                  });
                }),
        ],
      ),
      body: GridBuilder(
        isSelectionMode: isSelectionMode,
        selectedList: _selected,
        onSelectionChange: (bool x) {
          setState(() {
            isSelectionMode = x;
          });
        },
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
    );
  }
}

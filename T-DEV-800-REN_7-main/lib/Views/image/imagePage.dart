import 'package:flutter/material.dart';

class ImagePage extends StatefulWidget {
  const ImagePage({super.key, required this.title});

  final String title;

  @override
  State<ImagePage> createState() => _ImagePageState();
}

class _ImagePageState extends State<ImagePage> {
  int _counter = 0;

  void _incrementCounter() {
    setState(() {
      _counter++;
    });
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
            Text(
              'You are in the Image',
              style: Theme.of(context).textTheme.headlineMedium,
            ),
            ElevatedButton(
              onPressed: (){ Navigator.pop(context);},
              child: const Icon(Icons.add),
            ),
          ],
        ),
      ),
    );
  }
}
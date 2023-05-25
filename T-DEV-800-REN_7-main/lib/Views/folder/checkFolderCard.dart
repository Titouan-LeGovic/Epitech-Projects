import 'package:flutter/material.dart';
import 'package:front/Views/folder/folderPage.dart';

class CheckFolderCard extends StatefulWidget {
  final VoidCallback onChanged;
  final VoidCallback onClick;
  final bool value;
  final bool selected;
  final bool selectionMode;
  final String folderName;
  final List<int> galleries;

  CheckFolderCard(
      {super.key,
      required this.selected,
      required this.folderName,
      required this.value,
      required this.onChanged,
      required this.onClick,
      required this.selectionMode,
      required this.galleries});

  @override
  CheckFolderCardState createState() => CheckFolderCardState();
}

class CheckFolderCardState extends State<CheckFolderCard> {
  SampleItem? selectedMenu;
  void _toggle(int index) {
    if (widget.selectionMode) {
      if (widget.selected) {
        setState(() {});
      } else {
        Navigator.pushNamed(
            context, '/gallery/' + widget.galleries[index].toString());
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Container(
        child: Column(children: [
      widget.selected
          ? const Icon(
              Icons.folder_open,
              color: Colors.blue,
            )
          : const Icon(
              Icons.folder,
              color: Colors.black,
            ),
      widget.selected
          ? Text(
              widget.folderName,
              selectionColor: Colors.blue,
            )
          : Text(widget.folderName, selectionColor: Colors.black)
    ]));
  }
}

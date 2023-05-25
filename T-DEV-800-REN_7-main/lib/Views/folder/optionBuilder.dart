import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:front/Views/folder/folderPage.dart';

class OptionBuilder extends StatefulWidget {
  const OptionBuilder({
    super.key,
    required this.selectedList,
    required this.isSelectionMode,
    required this.onSelectionChange,
  });

  final bool isSelectionMode;
  final Function(bool)? onSelectionChange;
  final List<bool> selectedList;

  @override
  OptionBuilderState createState() => OptionBuilderState();
}

class OptionBuilderState extends State<OptionBuilder> {
  SampleItem? selectedMenu;
  void _toggle(int index) {
    if (widget.isSelectionMode) {
      setState(() {
        widget.selectedList[index] = !widget.selectedList[index];
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    if (widget.isSelectionMode) {
      return Container(
          height: MediaQuery.of(context).size.height * 0.05,
          child: PopupMenuButton<SampleItem>(
            icon: const Icon(Icons.pending),
            position: PopupMenuPosition.over,
            // shadowColor: Colors.blueGrey,
            initialValue: selectedMenu,
            onSelected: (SampleItem item) {
              setState(() {
                selectedMenu = item;
              });
            },
            itemBuilder: (BuildContext context) => <PopupMenuEntry<SampleItem>>[
              const PopupMenuItem<SampleItem>(
                value: SampleItem.itemName,
                child: const Icon(Icons.title),
              ),
              const PopupMenuItem<SampleItem>(
                value: SampleItem.itemRules,
                child: const Icon(Icons.lock_person),
              ),
              const PopupMenuItem<SampleItem>(
                value: SampleItem.itemDelete,
                child: const Icon(Icons.delete),
              ),
            ],
          ));
    } else {
      return Container(height: 0, width: 0);
    }
  }
}

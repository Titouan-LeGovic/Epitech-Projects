import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:front/Views/folder/checkFolderCard.dart';
import 'package:front/Views/folder/folderPage.dart';

class GridBuilder extends StatefulWidget {
  const GridBuilder({
    super.key,
    required this.selectedList,
    required this.isSelectionMode,
    required this.onSelectionChange,
  });

  final bool isSelectionMode;
  final Function(bool)? onSelectionChange;
  final List<bool> selectedList;

  @override
  GridBuilderState createState() => GridBuilderState();
}

class GridBuilderState extends State<GridBuilder> {
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
    return widget.isSelectionMode
        ? Container(
            height: MediaQuery.of(context).size.height * 0.74,
            child: GridView.builder(
                shrinkWrap: true,
                scrollDirection: Axis.vertical,
                itemCount: widget.selectedList.length,
                gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                    crossAxisCount: 4),
                itemBuilder: (_, int index) {
                  return InkWell(
                    onTap: () => _toggle(index),
                    onLongPress: () {
                      if (!widget.isSelectionMode) {
                        setState(() {
                          widget.selectedList[index] = true;
                        });
                        widget.onSelectionChange!(true);
                      }
                    },
                    child: GridTile(
                        child: Container(
                            child: CheckFolderCard(
                      folderName: 'Name',
                      selected: widget.selectedList[index],
                      value: widget.selectedList[index],
                      onChanged: () => _toggle(index),
                      onClick: () => _toggle(index),
                      selectionMode: widget.isSelectionMode,
                      galleries: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
                    ))),
                  );
                }))
        : Container(
            height: MediaQuery.of(context).size.height * 0.82,
            child: GridView.builder(
                shrinkWrap: true,
                scrollDirection: Axis.vertical,
                itemCount: widget.selectedList.length,
                gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                    crossAxisCount: 4),
                itemBuilder: (_, int index) {
                  return InkWell(
                    onTap: () => _toggle(index),
                    onLongPress: () {
                      if (!widget.isSelectionMode) {
                        setState(() {
                          widget.selectedList[index] = true;
                        });
                        widget.onSelectionChange!(true);
                      }
                    },
                    child: GridTile(
                        child: Container(
                            child: CheckFolderCard(
                      folderName: 'Name',
                      selected: false,
                      value: false,
                      onChanged: () => _toggle(index),
                      onClick: () => _toggle(index),
                      selectionMode: widget.isSelectionMode,
                      galleries: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
                    )
                            // child: Column(
                            //     children: [
                            //       const Icon(Icons.folder),
                            //       Text('Name'),
                            //     ]
                            )),
                  );
                }));
  }
}

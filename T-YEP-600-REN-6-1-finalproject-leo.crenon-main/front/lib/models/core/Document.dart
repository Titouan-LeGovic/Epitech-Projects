import 'Index.dart';

class Document {
  int id;
  String name;
  String type;
  String size;
  String path;
  bool toIndex;
  List<Index> listIndex;

  Document({
    required this.id,
    required this.name,
    required this.type,
    required this.size,
    required this.path,
    required this.toIndex,
    required this.listIndex,
  });
  factory Document.fromJson(Map<String, dynamic> json) {
    List<String> list = ["o", "ko", "Mo", "Go", "To"];
    int index = 0;
    try {
      double size = double.parse(json['size']);
      while (size > 1024) {
        size = (size / 1024);
        index++;
      }
      int i = size.round();
      Iterable indexes = json['indexes'];
      return Document(
        id: json['id'],
        name: json['name'].toString().replaceAll(".pdf", ""),
        type: json['type'],
        size: "$i ${list[index]}",
        path: json['path'],
        toIndex: json['toIndex'],
        listIndex: indexes.map((e) => Index.fromJson(e)).toList(),
      );
    } catch (e) {
      print("catch document $e");
      return Document(
        id: 0,
        name: "",
        type: "",
        size: "",
        path: "",
        toIndex: false,
        listIndex: [],
      );
    }
  }
}

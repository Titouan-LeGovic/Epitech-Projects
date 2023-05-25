import 'package:front/models/core/Document.dart';

import 'Rule.dart';

class CompleteFolder {
  int id;
  int iconIndex;
  String name;
  List<Rule> listRule;
  List<Document> listDocument;

  CompleteFolder({
    required this.id,
    required this.iconIndex,
    required this.name,
    required this.listRule,
    required this.listDocument,
  });

  factory CompleteFolder.fromJson(Map<String, dynamic> json) {
    try {
      Iterable rules = json["rules"];
      Iterable documents = json["documents"];
      return CompleteFolder(
        id: json['folder']["id"],
        iconIndex: json['folder']["icon"],
        name: json['folder']["name"],
        listRule: rules.map((e) => Rule.fromJson(e)).toList(),
        listDocument: documents.map((e) => Document.fromJson(e)).toList(),
      );
    } catch (e) {
      print("catch completeFolder $e");
      return CompleteFolder(
        id: 0,
        iconIndex: 0,
        name: "",
        listRule: [],
        listDocument: [],
      );
    }
  }
}

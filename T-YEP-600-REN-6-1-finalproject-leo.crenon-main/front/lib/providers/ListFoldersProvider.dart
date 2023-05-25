import 'package:flutter/material.dart';
import 'package:front/models/core/Folder.dart';
import 'package:front/models/core/Organization.dart';

class ListFoldersProvider extends ChangeNotifier {
  List<Folder> _listFolder = [];

  Organization? _organization;

  List<Folder> get listFolder => _listFolder;

  set listFolder(List<Folder> value) {
    _listFolder = value;
    notifyListeners();
  }

  Organization? get organization => _organization;

  set organization(Organization? value) {
    _organization = value;
    notifyListeners();
  }
}
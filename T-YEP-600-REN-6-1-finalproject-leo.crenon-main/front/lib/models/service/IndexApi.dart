import 'dart:convert';
import 'dart:io';

import 'package:front/models/core/Document.dart';
import 'package:front/models/core/Folder.dart';
import 'package:front/models/core/Index.dart';
import 'package:front/models/core/Rule.dart';
import 'package:front/providers/AuthenticationProvider.dart';
import 'package:dio/dio.dart';
import '/utils/Constants.dart' as constants;

class IndexApi {
  final Dio dio = Dio();
  Future<List<Index>?> getIndex(
      {required int idDocument,
      required AuthenticationProvider authentication}) async {
    print('getIndex');
    print('${constants.URL}/index/$idDocument');
    Response response;
    try {
      response = await dio.get(
        '${constants.URL}/index/$idDocument',
        options: Options(
          headers: {'Authorization': "Bearer ${authentication.user.token}"},
          contentType: "application/form-data",
        ),
      );
    } catch (e) {
      print("catch getIndex $e");
      return null;
    }
    print('responseBody getIndex: ${response.data}');

    if (response.statusCode == HttpStatus.ok) {
      List<Index> listIndex = [];
      Map<String, dynamic> json = response.data; //jsonDecode(response.data);
      Iterable iterable = json['list'];
      print(iterable);
      listIndex = iterable.map((i) => Index.fromJson(i)).toList();

      return listIndex;
    }

    return null;
  }

  Future<bool> putIndex(
      {required int idIndex,
      required String value,
      required AuthenticationProvider authentication}) async {
    print('putIndex');
    print('${constants.URL}/index/$idIndex');

    var formJson = jsonEncode(
      <String, dynamic>{
        "value": value,
      },
    );

    Response response;
    try {
      response = await dio.put(
        '${constants.URL}/index/$idIndex',
        options: Options(
          headers: {'Authorization': "Bearer ${authentication.user.token}"},
          contentType: "application/form-data",
        ),
        data: formJson,
      );
    } catch (e) {
      print("catch putIndex $e");
      return false;
    }
    print('responseBody putIndex: ${response.data}');

    if (response.statusCode == HttpStatus.ok) {
      return true;
    }

    return false;
  }
}

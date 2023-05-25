import 'package:flutter/material.dart';
import 'package:flutter/rendering.dart';
import 'package:front/models/core/Document.dart';
import 'package:front/models/core/Rule.dart';
import 'package:front/models/service/IndexApi.dart';
import 'package:front/providers/AuthenticationProvider.dart';
import 'package:front/providers/DrawerProvider.dart';
import 'package:front/providers/HomePageProvider.dart';
import 'package:provider/provider.dart';

import 'package:front/models/core/Index.dart';

class IndexHelper {
  BuildContext context;

  IndexApi indexApi = IndexApi();

  late AuthenticationProvider authentication;
  late HomePageProvider homePageProvider;

  IndexHelper({required this.context}) {
    authentication =
        Provider.of<AuthenticationProvider>(context, listen: false);
    homePageProvider = Provider.of<HomePageProvider>(context, listen: false);
  }

  Future<bool> putIndex(
      {required int idIndex,
      required String value,
      required AuthenticationProvider authentication}) async {
    bool retValue = await indexApi.putIndex(
      idIndex: idIndex,
      value: value,
      authentication: authentication,
    );
    if (!retValue) return false;
    try {
      homePageProvider.document!.listIndex
          .firstWhere((element) => element.id == idIndex)
          .value = value;
    } catch (e) {
      print("catch putIndex $e");
    }

    homePageProvider.document = homePageProvider.document;
    return false;
  }

  // Future<void> getIndexs({required int idDocument}) async {
  //   List<Index>? listIndex = await indexApi.getIndex(
  //     idDocument: idDocument,
  //     authentication: authentication,
  //   );
  //   if (listIndex == null) return;
  //   homePageProvider.listIndex = listIndex;
  //   return;
  // }
}

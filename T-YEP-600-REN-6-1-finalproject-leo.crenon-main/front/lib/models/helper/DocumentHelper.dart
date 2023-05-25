import 'package:flutter/cupertino.dart';
import 'package:front/models/core/Document.dart';
import 'package:front/models/core/Folder.dart';
import 'package:front/models/service/DocumentApi.dart';
import 'package:front/providers/AuthenticationProvider.dart';
import 'package:front/providers/HomePageProvider.dart';
import 'package:provider/provider.dart';

class DocumentHelper {
  BuildContext context;
  late AuthenticationProvider authentication;
  DocumentApi documentApi = DocumentApi();
  late HomePageProvider homePageProvider;

  DocumentHelper({required this.context}) {
    authentication =
        Provider.of<AuthenticationProvider>(context, listen: false);
    homePageProvider = Provider.of<HomePageProvider>(context, listen: false);
  }

  Future<void> deleteDocuments(
      {required List<Document> listDocument, required Folder folder}) async {
    for (Document document in listDocument) {
      await documentApi.delDocument(
        idDocument: document.id,
        authentication: authentication,
      );
      homePageProvider.completeFolder!.listDocument
          .removeWhere((element) => element.id == document.id);
      homePageProvider.completeFolder = homePageProvider.completeFolder;
    }
  }
}

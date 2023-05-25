import 'package:flutter/material.dart';
import 'package:front/models/core/Index.dart';
import 'package:front/models/helper/IndexHelper.dart';
import 'package:front/models/helper/DownloadHelper.dart';
import 'package:front/providers/AuthenticationProvider.dart';
import '../../../models/core/Document.dart';
import '../../../models/core/Rule.dart';
import '/providers/HomePageProvider.dart';
import 'package:provider/provider.dart';

class FileWidget extends StatefulWidget {
  const FileWidget({Key? key}) : super(key: key);

  @override
  State<FileWidget> createState() => _FileWidgetState();
}

class _FileWidgetState extends State<FileWidget> {
  ScrollController scrollController = ScrollController();

  late HomePageProvider homePageProvider;
  late AuthenticationProvider authentication;

  late IndexHelper indexHelper;

  DownloadHelper downloadHelper = DownloadHelper();

  @override
  void initState() {
    super.initState();

    homePageProvider = Provider.of<HomePageProvider>(context, listen: false);
    authentication =
        Provider.of<AuthenticationProvider>(context, listen: false);

    indexHelper = IndexHelper(context: context);
  }

  @override
  Widget build(BuildContext context) {
    return Selector<HomePageProvider, Document?>(
        selector: (_, provider) => provider.document,
        builder: (_, document, __) {
          if (document != null) {
            return Expanded(
              flex: 20,
              child: Column(
                children: [
                  Expanded(
                    child: Scrollbar(
                      controller: scrollController,
                      thumbVisibility: true,
                      trackVisibility: true,
                      child: SingleChildScrollView(
                        controller: scrollController,
                        child: Column(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                                SizedBox(height: 20),
                                Card(
                                  color: Colors.grey.shade200,
                                  elevation: 3,
                                  child: Container(
                                    padding: EdgeInsets.all(20),
                                    child: Column(
                                      children: [
                                        SizedBox(
                                          height: 20,
                                          width: double.infinity,
                                        ),
                                        Text(
                                          document.name,
                                          style: TextStyle(fontSize: 20),
                                        ),
                                        SizedBox(
                                          height: 20,
                                        ),
                                        Text(
                                          "Size : ${homePageProvider.document!.size}",
                                        ),
                                        SizedBox(
                                          height: 10,
                                        ),
                                      ],
                                    ),
                                  ),
                                ),
                                SizedBox(
                                  height: 20,
                                ),
                                Text("Associated tags"),
                                SizedBox(
                                  height: 10,
                                ),
                              ] +
                              () {
                                List<Widget> listWidget = [];
                                for (int i = 0;
                                    i <
                                        homePageProvider
                                            .completeFolder!.listRule.length;
                                    i++) {
                                  Rule rule = homePageProvider
                                      .completeFolder!.listRule[i];
                                  Index index =
                                      homePageProvider.document!.listIndex[i];
                                  TextEditingController textEditingController =
                                      TextEditingController(text: index.value);
                                  listWidget.add(
                                    Card(
                                      color: Colors.grey.shade100,
                                      child: Container(
                                        padding: EdgeInsets.symmetric(
                                          horizontal: 20,
                                        ),
                                        margin: EdgeInsets.all(8),
                                        child: Row(
                                          children: [
                                            Text(
                                              "${rule.name} :",
                                            ),
                                            SizedBox(
                                              width: 10,
                                            ),
                                            Expanded(
                                              child: TextField(
                                                controller:
                                                    textEditingController,
                                              ),
                                            ),
                                            SizedBox(
                                              width: 10,
                                            ),
                                            ElevatedButton(
                                              style: ButtonStyle(
                                                backgroundColor:
                                                    MaterialStateProperty.all<
                                                        Color>(Colors.white),
                                              ),
                                              onPressed: () async {
                                                //TODO
                                                indexHelper.putIndex(
                                                  idIndex: index.id,
                                                  value: textEditingController
                                                      .text,
                                                  authentication:
                                                      authentication,
                                                );
                                              },
                                              child: Row(
                                                children: [
                                                  Icon(
                                                    Icons.edit,
                                                    color: Colors.orange,
                                                  ),
                                                ],
                                              ),
                                            ),
                                          ],
                                        ),
                                      ),
                                    ),
                                  );
                                }
                                return listWidget;
                              }(),
                        ),
                      ),
                    ),
                  ),
                  TextButton(
                      onPressed: () {
                        downloadHelper.downloadFile(
                            user: authentication.user,
                            listDocument: [document]);
                      },
                      child: Text("download"))
                ],
              ),
            );
          } else {
            return Container();
          }
        });
  }
}

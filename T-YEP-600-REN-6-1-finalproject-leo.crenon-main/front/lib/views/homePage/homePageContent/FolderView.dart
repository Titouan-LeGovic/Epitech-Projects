import 'package:file_picker/file_picker.dart';
import 'package:flutter/material.dart';
import 'package:front/models/core/CompleteFolder.dart';
import 'package:front/models/helper/DocumentHelper.dart';
import 'package:front/models/helper/DownloadHelper.dart';
import 'package:front/models/helper/FolderHelper.dart';
import 'package:front/models/helper/UploadHelper.dart';
import 'package:front/models/service/UploadFileApi.dart';
import 'package:front/providers/AuthenticationProvider.dart';
import 'package:front/views/homePage/dialog/ConfirmDialog.dart';
import 'package:front/views/homePage/dialog/UpdateFolderDialog.dart';
import '../../../models/core/Document.dart';
import 'package:provider/provider.dart';
import 'package:tuple/tuple.dart';

import '../../../models/core/Folder.dart';
import '../../../models/core/Index.dart';
import '../../../models/core/Rule.dart';
import '../../../providers/AppBarProvider.dart';
import '../../../providers/HomePageProvider.dart';

class FolderWidget extends StatefulWidget {
  const FolderWidget({Key? key}) : super(key: key);

  @override
  State<FolderWidget> createState() => _FolderWidgetState();
}

class _FolderWidgetState extends State<FolderWidget> {
  late HomePageProvider homePageProvider;
  late AppBarProvider appBarProvider;
  late AuthenticationProvider authentication;

  ScrollController scrollController = ScrollController();

  @override
  void initState() {
    super.initState();
    homePageProvider = Provider.of<HomePageProvider>(context, listen: false);
    appBarProvider = Provider.of<AppBarProvider>(context, listen: false);
    authentication =
        Provider.of<AuthenticationProvider>(context, listen: false);
  }

  List<Widget> listFile(List<Document> listDocument) {
    List<Widget> listWidget = [
      SizedBox(
        height: 30,
      )
    ];

    for (Document document in listDocument) {
      bool display = true;
      if (appBarProvider.isRuleOpen) {
        for (Index index in homePageProvider.listSearchIndex) {
          if (index.value.isEmpty) continue;
          if (document.listIndex
              .firstWhere((element) => element.ruleId == index.id)
              .value
              .toLowerCase()
              .contains(index.value.toLowerCase())) {
            display = display && true;
          } else {
            display = false;
          }
        }
      }
      if (!display) continue;
      if (homePageProvider.fileName.isNotEmpty &&
          !document.name
              .toLowerCase()
              .contains(homePageProvider.fileName.toLowerCase())) continue;
      listWidget.add(
        GestureDetector(
          onTap: () {
            if (homePageProvider.document != null) {
              if (homePageProvider.document!.id == document.id) {
                homePageProvider.document = null;
              } else {
                homePageProvider.document = document;
              }
            } else {
              homePageProvider.document = document;
            }
          },
          child: Container(
            height: 100,
            color: Colors.grey[200],
            padding: EdgeInsets.only(left: 10, right: 20),
            child: Row(
              children: [
                Checkbox(
                  value: (homePageProvider.listDownload
                          .indexWhere((element) => element.id == document.id) !=
                      -1),
                  onChanged: (value) {
                    if (value == null) return;
                    value
                        ? homePageProvider.listDownload.add(document)
                        : homePageProvider.listDownload.removeWhere(
                            (element) => element.id == document.id);
                    homePageProvider.listDownload =
                        homePageProvider.listDownload;
                  },
                ),
                Text(document.name),
                Spacer(),
                Text(document.size),
                SizedBox(
                  width: 10,
                ),
                Text(document.type)
              ],
            ),
          ),
        ),
      );
      listWidget.add(
        SizedBox(
          height: 10,
        ),
      );
    }

    return listWidget;
  }

  @override
  Widget build(BuildContext context) {
    return Selector3<AppBarProvider, HomePageProvider, HomePageProvider,
        Tuple3<bool, Document?, List<Document>>>(
      selector: (_, provider1, provider2, provider3) => Tuple3(
          provider1.isRuleOpen, provider2.document, provider3.listDownload),
      shouldRebuild: ((previous, next) => true),
      builder: (_, data, __) {
        bool isAll = homePageProvider.completeFolder!.listDocument.length ==
                homePageProvider.listDownload.length &&
            homePageProvider.listDownload.isNotEmpty;
        return Expanded(
          flex: data.item2 != null
              ? (data.item1 ? 40 : 65)
              : (data.item1 ? 60 : 85),
          child: Column(
            children: [
              Container(
                height: 50,
                alignment: Alignment.centerLeft,
                color: Colors.grey.shade300,
                child: Row(
                  children: [
                    SizedBox(
                      width: 15,
                    ),
                    Text(
                      homePageProvider.completeFolder!.name,
                      style: TextStyle(fontSize: 20, color: Colors.blue),
                    ),
                    Spacer(),
                    Padding(
                      padding: const EdgeInsets.all(8.0),
                      child: ElevatedButton(
                        style: ButtonStyle(
                          backgroundColor:
                              MaterialStateProperty.all<Color>(Colors.white),
                        ),
                        onPressed: () async {
                          await updateFolderDialog(
                            context: context,
                            folder: homePageProvider.folder!,
                            listRule: homePageProvider.completeFolder!.listRule
                                .toList(),
                          );
                        },
                        child: Row(
                          children: [
                            Text(
                              "Update Folder",
                              style: TextStyle(color: Colors.orange),
                            ),
                            Icon(
                              Icons.edit,
                              color: Colors.orange,
                            ),
                          ],
                        ),
                      ),
                    ),
                    Padding(
                      padding: const EdgeInsets.all(8.0),
                      child: ElevatedButton(
                        style: ButtonStyle(
                          backgroundColor:
                              MaterialStateProperty.all<Color>(Colors.white),
                        ),
                        onPressed: () {
                          ConfirmDialog(
                              context: context,
                              function: () async {
                                FolderHelper folderHelper =
                                    FolderHelper(context: context);
                                folderHelper.deleteFolder(
                                  idFolder: homePageProvider.folder!.id,
                                );
                                Navigator.of(context).pop();
                              });
                        },
                        child: Row(
                          children: [
                            Text(
                              "Delete Folder",
                              style: TextStyle(color: Colors.red),
                            ),
                            Icon(
                              Icons.delete,
                              color: Colors.red,
                            ),
                          ],
                        ),
                      ),
                    ),
                  ],
                ),
              ),
              Container(
                height: 50,
                alignment: Alignment.centerLeft,
                color: Colors.grey.shade300,
                child: Row(
                  children: [
                    Checkbox(
                      value: isAll,
                      onChanged: ((value) {
                        if (isAll) {
                          homePageProvider.listDownload.clear();
                          homePageProvider.listDownload =
                              homePageProvider.listDownload;
                        } else {
                          homePageProvider.listDownload.clear();
                          homePageProvider.listDownload.addAll(
                              homePageProvider.completeFolder!.listDocument);
                          homePageProvider.listDownload =
                              homePageProvider.listDownload;
                        }
                      }),
                    ),
                    Padding(
                      padding: const EdgeInsets.all(8.0),
                      child: ElevatedButton(
                        style: ButtonStyle(
                          backgroundColor:
                              MaterialStateProperty.all<Color>(Colors.white),
                        ),
                        onPressed: () async {
                          FilePickerResult? result;
                          UploadFileHelper uploadFileHelper =
                              UploadFileHelper(context: context);
                          try {
                            result = await FilePicker.platform.pickFiles(
                              allowMultiple: true,
                              type: FileType.custom,
                              allowedExtensions: ['pdf'],
                            );
                          } catch (e) {
                            return;
                          }
                          if (result == null) return;
                          uploadFileHelper.uploadFile(
                            listFiles: result.files,
                            user: authentication.user,
                            folder: homePageProvider.folder!,
                          );
                        },
                        child: Row(
                          children: [
                            Text(
                              "Upload",
                              style: TextStyle(color: Colors.blue),
                            ),
                            Icon(
                              Icons.upload,
                              color: Colors.blue,
                            ),
                          ],
                        ),
                      ),
                    ),
                    Padding(
                      padding: const EdgeInsets.all(8.0),
                      child: ElevatedButton(
                        style: ButtonStyle(
                          backgroundColor:
                              MaterialStateProperty.all<Color>(Colors.white),
                        ),
                        onPressed: () async {
                          if (homePageProvider.listDownload.isEmpty) return;
                          DownloadHelper downloadHelper = DownloadHelper();
                          downloadHelper.downloadFile(
                            user: authentication.user,
                            listDocument: homePageProvider.listDownload,
                          );
                          homePageProvider.listDownload = [];
                        },
                        child: Row(
                          children: [
                            Text(
                              "Download",
                              style: TextStyle(color: Colors.blue),
                            ),
                            Icon(
                              Icons.download,
                              color: Colors.blue,
                            ),
                          ],
                        ),
                      ),
                    ),
                    Padding(
                      padding: const EdgeInsets.all(8.0),
                      child: ElevatedButton(
                        style: ButtonStyle(
                          backgroundColor:
                              MaterialStateProperty.all<Color>(Colors.white),
                        ),
                        onPressed: () {
                          ConfirmDialog(
                            context: context,
                            function: () async {
                              if (homePageProvider.listDownload.isEmpty) {
                                return;
                              }
                              DocumentHelper documentHelper =
                                  DocumentHelper(context: context);
                              documentHelper.deleteDocuments(
                                listDocument: homePageProvider.listDownload,
                                folder: homePageProvider.folder!,
                              );
                              homePageProvider.listDownload = [];
                              Navigator.of(context).pop();
                            },
                          );
                        },
                        child: Row(
                          children: [
                            Text(
                              "Delete selected files",
                              style: TextStyle(color: Colors.red),
                            ),
                            Icon(
                              Icons.delete,
                              color: Colors.red,
                            ),
                          ],
                        ),
                      ),
                    ),
                  ],
                ),
              ),
              Expanded(
                child: Scrollbar(
                  controller: scrollController,
                  thumbVisibility: true,
                  child: SingleChildScrollView(
                    controller: scrollController,
                    child: Selector<HomePageProvider, CompleteFolder?>(
                      selector: (_, provider) => provider.completeFolder,
                      shouldRebuild: (previous, next) => true,
                      builder: (_, listDocument, __) {
                        return Column(
                          mainAxisSize: MainAxisSize.min,
                          children: listFile(
                            homePageProvider.completeFolder!.listDocument,
                          ),
                        );
                      },
                    ),
                  ),
                ),
              ),
            ],
          ),
        );
      },
    );
  }
}

import 'dart:io';

import 'package:flutter/material.dart';
import 'package:front/models/core/CompleteFolder.dart';
import 'package:front/models/core/Document.dart';
import 'package:front/models/core/Rule.dart';
import 'package:front/models/helper/DocumentHelper.dart';
import 'package:front/models/helper/RuleHelper.dart';
import 'package:front/models/service/FolderApi.dart';
import 'package:front/providers/AuthenticationProvider.dart';
import 'package:front/providers/DrawerProvider.dart';
import 'package:front/providers/HomePageProvider.dart';
import 'package:front/providers/ListFoldersProvider.dart';
import 'package:provider/provider.dart';

import 'package:front/models/core/Folder.dart';

class FolderHelper {
  BuildContext context;

  FolderApi folderApi = FolderApi();
  late DocumentHelper documentHelper;
  late RuleHelper ruleHelper;

  late AuthenticationProvider authentication;
  late DrawerProvider drawerProvider;
  late HomePageProvider homePageProvider;

  FolderHelper({required this.context, DocumentHelper? documentHelperStack}) {
    drawerProvider = Provider.of<DrawerProvider>(context, listen: false);
    authentication =
        Provider.of<AuthenticationProvider>(context, listen: false);
    homePageProvider = Provider.of<HomePageProvider>(context, listen: false);
    if (documentHelperStack == null) {
      documentHelper = DocumentHelper(context: context);
    } else {
      documentHelper = documentHelperStack;
    }

    ruleHelper = RuleHelper(context: context);
  }

  Future<void> getFolders({
    DrawerProvider? drawerProvider,
    ListFoldersProvider? listFoldersProvider,
  }) async {
    if (drawerProvider != null && listFoldersProvider == null) {
      if (drawerProvider.organization == null) return;
      List<Folder>? listFolder = await folderApi.getFolders(
        idOrganization: drawerProvider.organization!.id,
        authentication: authentication,
      );
      if (listFolder == null) return;
      drawerProvider.listFolder = listFolder;
      return;
    } else if (listFoldersProvider != null && drawerProvider == null) {
      List<Folder>? listFolder = await folderApi.getFolders(
        idOrganization: listFoldersProvider.organization!.id,
        authentication: authentication,
      );
      if (listFolder == null) return;
      listFoldersProvider.listFolder = listFolder;
      return;
    } else {
      return;
    }
  }

  Future<void> getFolderContent({required Folder folder}) async {
    CompleteFolder? completeFolder = await folderApi.getCompleteFolder(
      idFolder: folder.id,
      authentication: authentication,
    );
    if (completeFolder == null) return;
    homePageProvider.completeFolder = completeFolder;
    homePageProvider.listRule = completeFolder.listRule;
  }

  Future<void> createFolder(
      {required List<Rule> listRule, required Folder value}) async {
    if (drawerProvider.organization == null) return;
    Folder? folder = await folderApi.createFolder(
        idOrganization: drawerProvider.organization!.id,
        folder: value,
        authentication: authentication);
    if (folder == null) return;
    for (Rule rule in listRule) {
      await ruleHelper.createFolderRule(
        idFolder: folder.id,
        rule: rule,
      );
    }
    getFolders(drawerProvider: drawerProvider);
  }

  Future<void> deleteFolder({required int idFolder}) async {
    int response = await folderApi.deleteFolder(
        idFolder: idFolder, authentication: authentication);
    if (response == HttpStatus.noContent) {
      getFolders(drawerProvider: drawerProvider);
    }
  }

  Future<void> updateFolder(
      {required int idFolder,
      required Folder folder,
      required List<Rule> listRule}) async {
    int response = await folderApi.updateFolder(
      idFolder: idFolder,
      folder: folder,
      authentication: authentication,
    );
    drawerProvider.listFolder
        .firstWhere((element) => element.id == idFolder)
        .name = folder.name;
    drawerProvider.listFolder
        .firstWhere((element) => element.id == idFolder)
        .iconIndex = folder.iconIndex;
    drawerProvider.listFolder = drawerProvider.listFolder;
    await updateFolderRules(idFolder: idFolder, listRule: listRule);
    await getFolderContent(folder: folder);
  }

  Future<void> updateFolderRules(
      {required int idFolder, required List<Rule> listRule}) async {
    for (Rule rule in listRule) {
      await ruleHelper.updateFolderRule(
        rule: rule,
        idFolder: idFolder,
      );
    }
  }
}

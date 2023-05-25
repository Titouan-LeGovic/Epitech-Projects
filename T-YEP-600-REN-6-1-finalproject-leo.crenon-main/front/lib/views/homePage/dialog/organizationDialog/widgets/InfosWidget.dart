import 'package:flutter/material.dart';
import 'package:front/models/core/Organization.dart';
import 'package:front/models/core/UserOrg.dart';
import 'package:front/models/helper/OrganizationHelper.dart';
import 'package:front/providers/AuthenticationProvider.dart';
import 'package:front/providers/DrawerProvider.dart';
import 'package:front/providers/HomePageProvider.dart';
import 'package:front/providers/ListFoldersProvider.dart';
import 'package:front/providers/OrganizationDialogProvider.dart';
import 'package:front/views/homePage/dialog/ConfirmDialog.dart';
import 'package:front/views/homePage/dialog/JoinOrganizationDialog.dart';
import 'package:provider/provider.dart';

import '../../../../../providers/OrganizationProvider.dart';

Widget infosWidget({
  required Organization organization,
  required BuildContext context,
}) {
  OrganizationHelper organizationHelper = OrganizationHelper(context: context);
  AuthenticationProvider authentication =
      Provider.of<AuthenticationProvider>(context, listen: false);
  ListFoldersProvider listFoldersProvider =
      Provider.of<ListFoldersProvider>(context, listen: false);
  DrawerProvider drawerProvider =
      Provider.of<DrawerProvider>(context, listen: false);
  return Column(
    children: [
      Container(
        padding: EdgeInsets.all(15),
        child: Row(
          children: [
            Text(
              "Owner : ${organization.owner.firstname} ${organization.owner.lastName}",
            ),
            Spacer(),
            organization.owner.id == authentication.user.id
                ? Container()
                : Padding(
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
                              organizationHelper.leaveOrganization(
                                idOrganization: organization.id,
                                idUser: authentication.user.id,
                              );
                              authentication.user.listOrganization.removeWhere(
                                  (element) => element.id == organization.id);
                              authentication.token = authentication.token;
                              drawerProvider.listFolder = [];
                              drawerProvider.organization = null;
                              listFoldersProvider.organization = null;
                              Navigator.of(context).pop();
                              Navigator.of(context).pop();
                            });
                      },
                      child: Row(
                        children: [
                          Text(
                            "Leave Organization",
                            style: TextStyle(color: Colors.red),
                          ),
                          Icon(
                            Icons.person_off,
                            color: Colors.red,
                          ),
                        ],
                      ),
                    ),
                  ),
            organization.owner.id != authentication.user.id
                ? Container()
                : Padding(
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
                              organizationHelper.deleteOrganization(
                                idOrganization: organization.id,
                              );
                              Navigator.of(context).pop();
                            });
                      },
                      child: Row(
                        children: [
                          Text(
                            "Delete Organization",
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
        margin: EdgeInsets.only(right: 8),
        padding: EdgeInsets.symmetric(horizontal: 10),
        alignment: Alignment.center,
        color: Colors.grey.shade200,
        child: Row(
          children: [
            Text("User list"),
            Spacer(),
            IconButton(
              onPressed: () {
                JoinOrganizationDialog(
                    context: context, idOrganization: organization.id);
              },
              icon: Icon(
                Icons.add,
              ),
            ),
          ],
        ),
      ),
      FutureBuilder<void>(
          future: organizationHelper.getOrganizationUsers(
              idOrganization: organization.id),
          builder: ((context, snapshot) {
            if (snapshot.connectionState != ConnectionState.done) {
              return Container();
            }
            return Selector<OrganizationDialogProvider, List<UserOrg>>(
                selector: (context, provider) => provider.listUserOrg,
                builder: (context, data, child) {
                  return SingleChildScrollView(
                    child: Column(
                      children: data.map<Widget>((userOrg) {
                        return Container(
                          padding: EdgeInsets.symmetric(
                              vertical: 10, horizontal: 10),
                          child: Row(
                            children: [
                              Text(
                                "${userOrg.firstname} ${userOrg.lastName} ${userOrg.email}",
                              ),
                              Spacer(),
                              (userOrg.id == organization.owner.id) ||
                                      (authentication.user.id !=
                                          organization.owner.id)
                                  ? Container()
                                  : Row(
                                      children: [
                                        TextButton(
                                            onPressed: () {
                                              ConfirmDialog(
                                                context: context,
                                                function: (() {
                                                  organizationHelper
                                                      .editOrganization(
                                                    name: organization.name,
                                                    ownerId: userOrg.id,
                                                    idOrganization:
                                                        organization.id,
                                                  );
                                                  Navigator.of(context).pop();
                                                }),
                                              );
                                            },
                                            child: Text("Give ownerships")),
                                        IconButton(
                                          onPressed: () {
                                            ConfirmDialog(
                                                context: context,
                                                function: () {
                                                  organizationHelper
                                                      .leaveOrganization(
                                                    idUser: userOrg.id,
                                                    idOrganization:
                                                        organization.id,
                                                  );
                                                  Navigator.of(context).pop();
                                                });
                                          },
                                          icon: Icon(
                                            Icons.person_remove,
                                            color: Colors.red,
                                          ),
                                        ),
                                      ],
                                    ),
                            ],
                          ),
                        );
                      }).toList(),
                    ),
                  );
                });
          })),
    ],
  );
}

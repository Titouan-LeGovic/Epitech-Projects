import 'package:flutter/material.dart';
import 'package:front/models/core/Organization.dart';
import 'package:front/models/helper/OrganizationHelper.dart';
import 'package:front/providers/OrganizationProvider.dart';
import 'package:provider/provider.dart';

Widget CreateOrganizationForm({
  required BuildContext context,
  required bool isEdit,
  Organization? organization,
}) {
  OrganizationHelper organizationHelper = OrganizationHelper(context: context);
  OrganizationProvider organizationProvider =
      Provider.of<OrganizationProvider>(context, listen: false);
  GlobalKey<FormState> organizationKey = GlobalKey<FormState>();
  return Form(
    key: organizationKey,
    child: Container(
      width: 600,
      margin: EdgeInsets.fromLTRB(20.0, 30.0, 20.0, 10.0),
      child: Column(mainAxisSize: MainAxisSize.min, children: [
        Text(isEdit
            ? " New name for your organization"
            : " Name for your new organization"),
        SizedBox(height: 10),
        TextFormField(
          initialValue: organization == null ? "" : organization.name,
          maxLength: 25,
          decoration: const InputDecoration(
              labelText: 'Nom de l\'organization',
              counterText: "",
              hintText: 'Entrer du texte',
              border: OutlineInputBorder()),
          validator: (value) {
            if (value == null) {
              return 'Veuillez saisir un texte';
            }
            organizationProvider.name = value;
            return null;
          },
        ),
        SizedBox(height: 10),
        ElevatedButton(
          onPressed: () {
            if (organizationKey.currentState!.validate()) {
              isEdit
                  ? organizationHelper.editOrganization(
                      name: organizationProvider.name,
                      ownerId: organization!.owner.id,
                      idOrganization: organization.id,
                    )
                  : organizationHelper.createOrganization(
                      name: organizationProvider.name,
                    );
            }
            Navigator.of(context).pop();
          },
          child: Text("Add Organization"),
        ),
        SizedBox(height: 10),
      ]),
    ),
  );
}

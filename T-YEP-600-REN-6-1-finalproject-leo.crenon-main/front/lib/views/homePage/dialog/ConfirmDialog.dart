import 'package:flutter/material.dart';

void ConfirmDialog(
    {required BuildContext context, required Function() function}) {
  showDialog(
      context: context,
      builder: (context) {
        return Dialog(
          shape:
              RoundedRectangleBorder(borderRadius: BorderRadius.circular(20.0)),
          child: SizedBox(
            height: 90,
            child: Column(children: [
              Padding(
                padding: EdgeInsets.all(
                  15,
                ),
                child: Text(
                  "Are you sure ?",
                ),
              ),
              Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  TextButton(
                      onPressed: () => Navigator.of(context).pop(),
                      child: Text("No")),
                  TextButton(onPressed: function, child: Text("Yes")),
                ],
              )
            ]),
          ),
        );
      });
}

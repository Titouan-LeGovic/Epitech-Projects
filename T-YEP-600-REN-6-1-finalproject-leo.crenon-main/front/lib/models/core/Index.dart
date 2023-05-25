class Index {
  int id;
  String value;
  int ruleId;

  Index({
    required this.id,
    required this.value,
    required this.ruleId,
  });

  factory Index.fromJson(Map<String, dynamic> json) {
    try {
      return Index(
        id: json["id"],
        value: json["value"],
        ruleId: json["rule_id"],
      );
    } catch (e) {
      print("catch index $e");
      return Index(
        id: 0,
        value: "",
        ruleId: 0,
      );
    }
  }
}

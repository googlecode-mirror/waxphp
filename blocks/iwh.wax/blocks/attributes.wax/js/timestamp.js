function attr_timestamp_set_custom(id) {
  var select = $("model_" + id + "_options_format");
  select.selectedIndex = select.childElementCount - 1;
}
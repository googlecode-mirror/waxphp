function update_attr_list(attr_list, with_attrs) {
  attr_list = $(attr_list);
  
  while (attr_list.childNodes.length > 0) {
    attr_list.removeChild(attr_list.childNodes[0]);
  }
  
  for (x = 0; x < with_attrs.length; x++) {
    var newopt = document.createElement("option");
    newopt.setAttribute("value",with_attrs[x]);
    newopt.innerHTML = with_attrs[x];
    attr_list.appendChild(newopt);
  }
}
<?php
    class AttributeViewNotFoundException extends WaxException {
        function __construct($attr_data, $action) {
            parent::__construct("View Not Found For: $attr_data[type]","Could not find necessary views.  Attr_data: <pre>" . print_r($attr_data,true) . "</pre>");
        }
    }
    class AttributeTypeNotFoundException extends WaxException {
        function __construct($attr_data, $action) {
            parent::__construct("$attr_data[type] is not a valid attribute data type","The $attr_data[type] attribute type could not be found");
        }
    }
?>
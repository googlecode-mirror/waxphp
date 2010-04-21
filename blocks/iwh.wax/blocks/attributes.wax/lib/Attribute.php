<?php
    class Attribute extends DCIObject implements rRenderableAttribute {
        private $_id;
        private $_name;
        private $_type;
        private $_default;
        private $_value;
        private $_label;
        private $_options;
        
        function __construct($attr_data) {
            parent::__construct();
            
            $this->_id = $attr_data['id'];
            $this->_name = (isset($attr_data['name']) ? $attr_data['name'] : "");
            $this->_type = (isset($attr_data['type']) ? $attr_data['type'] : "textfield");
            $this->_default = (isset($attr_data['default']) ? $attr_data['default'] : "");
            $this->_value = (isset($attr_data['value']) ? $attr_data['value'] : "");
            $this->_label = (isset($attr_data['label']) ? $attr_data['label'] : $this->_name);
            $this->_options = (isset($attr_data['options']) ? $attr_data['options'] : array());
        }
        
        function GetID() { return $this->_id; }
        function GetName() { return $this->_name; }
        function GetType() { return $this->_type; }
        function GetDefault() { return $this->_default; }
        function GetValue() { return $this->_value; }
        function GetLabel() { return $this->_label; }
        function GetOptions() { return $this->_options; }
    }
?>
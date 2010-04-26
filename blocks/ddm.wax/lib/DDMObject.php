<?php
    /**
    * DDMObject encapsulates a data record and allows for more advanced
    * functionality to be added/injected to it.  This is primarily used
    * when an object needs 
    */
    class DDMObject extends DCIObject {
        private $attrs;
        
        function __construct($data) {
            parent::__construct();
            $this->attrs = $data;
        }
        function __call($func,$args) {
            if (preg_match("/^Get/",$func)) {
                $func = substr($var,3);
                if (isset($this->attrs[$func]))
                    return $this->attrs[$func];
                else
                    throw new AttributeNotFoundException($func,$this);
            }
            else 
                return parent::__call($func,$args);
        }
    }
?>
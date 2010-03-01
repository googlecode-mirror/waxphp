<?php
    class QueryString extends DCIObject implements rRouter {
        private $urlparts = array("context","action");
        
        function GetAliases() { 
            return $this->urlparts; 
        }
    }
?>
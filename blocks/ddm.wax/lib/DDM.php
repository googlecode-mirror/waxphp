<?php
    class DDM extends WaxObject implements rScaffolder, rDynamicModelHandler {
        var $type;
        
        function initialize($type = '') {
            $this->type = $type;
        }
        function GetType() { return $this->type; }
    }
?>
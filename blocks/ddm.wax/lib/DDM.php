<?php
    class DDM extends WaxObject implements rScaffolder, rDynamicModelHandler {
        var $type = NULL;
        
        function initialize($type = NULL) {
            $this->type = $type;
        }
        function GetType() { 
            if (is_null($this->type)) return get_class($this);
            else return $this->type; 
        }
    }
?>
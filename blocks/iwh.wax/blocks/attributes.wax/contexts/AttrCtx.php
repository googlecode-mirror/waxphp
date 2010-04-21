<?php
    class AttrCtx extends ControllerCtx {
        protected $attribute = NULL;
        
        // override controller execute ctx
        function Execute(Attribute $attribute, $action) {
            $this->attribute = $attribute;
            if (method_exists($this,$action)) {
                $this->$action();
            }
            return $this->view;
        }
    }
?>
<?php
    class View extends DCIObject implements rRenderable {
        function __construct($block, $viewname) {
            parent::__construct();
            
            $this->block = $block;
            $this->viewname = $viewname;
        }
        function __toString() {
            return $this->Render(array(),true);
        }
    }
?>
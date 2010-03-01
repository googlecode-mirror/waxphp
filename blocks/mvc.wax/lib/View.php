<?php
    /**
    * This class is repsonsible for holding information
    * for a View.  The class implements rRenderable to actually
    * display the view.
    * 
    * @author Joe Chrzanowski
    * @version 0.10
    */
    class View extends DCIObject implements rRenderable {
        function __construct($block, $viewname) {
            parent::__construct();
            
            $this->block = $block;
            $this->viewname = $viewname;
        }
        function GetViewName() {
            return $this->viewname;
        }
        function GetViewBlock() {
            if (is_string($this->block))
                return BlockManager::GetBlock($this->block);

            return $this->block;
        }
        function __toString() {
            return $this->Render(array(),true);
        }
    }
?>
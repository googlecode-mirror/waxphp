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
        protected $block;
        protected $name;
        
        function __construct($name, WaxBlock $parent_block = NULL) {
            parent::__construct();
            
            $this->block = $parent_block;
            $this->name = $name;
        }
        function GetViewName() {
            return $this->name;
        }
        function GetViewBlock() {
            return $this->block;
        }
        function __toString() {
            return $this->Render(array(),true);
        }
    }
?>
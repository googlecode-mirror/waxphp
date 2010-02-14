<?php
    require_once "View.php";
    
    class Layout extends View {
        function __construct($viewfile = NULL) {
            parent::__construct();
            if (!is_null($viewfile)) $this->viewfile = $viewfile;
        }
    }
?>
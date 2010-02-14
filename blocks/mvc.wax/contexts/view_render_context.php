<?php
    class ViewRenderCtx extends Context {
        var $view;
        var $args;
        
        function __construct($view, $args) {
            $this->view = $view;
            $this->args = $args;
        }
        function Execute() {
            return $this->view->Render($this->args);
        }
    }
?>
<?php
    class Router 
    extends DCIObject 
    implements rQueryStringRouter
    {
        function GetTargetView() {
            $controller = $this->controller;
            $action = $this->action;
            
            return (empty($controller) ? "Default" : $controller) . "/" . (empty($action) ? "index" : $action);
        }
    }
?>
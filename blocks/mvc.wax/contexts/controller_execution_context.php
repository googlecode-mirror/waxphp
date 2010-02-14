<?php
    class ControllerExecutionCtx extends Context {
        protected $controller;
        protected $action;
        
        function __construct($app) {
            $this->controller = $app->controller;
            $this->action = $app->action;
            
            if (is_null($this->controller) || empty($this->controller)) 
                $this->controller = "Default";
            $this->controller .= "Controller";
            
            if (is_null($this->action) || empty($this->action)) $this->action = "index";
        }
        function Execute() {
            if (class_exists($this->controller)) {
                $class = $this->controller;
                $ctrller = new $class($this->action);
                return $ctrller->Execute();
            }
            else throw new ContextNotFoundException($this->action, $this->controller);
        }
    }
?>
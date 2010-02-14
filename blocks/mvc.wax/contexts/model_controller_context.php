<?php
    class ModelNotFoundException extends WaxException {
        function __construct($modelname) {
            parent::__construct("Model Not Found","Model: '$modelname'");
        }
    }
    abstract class ModelControllerCtx extends ControllerCtx {
        var $models = array();
        
        function __construct($action = NULL) {
            parent::__construct($action);
            
            $name = str_replace(array("Controller","Ctx"),"",get_class($this));
            if (class_exists($name)) {
                $this->models[strtolower($name)] = new $name();
            }
            else throw new ModelNotFoundException($name);
        }
    }
?>
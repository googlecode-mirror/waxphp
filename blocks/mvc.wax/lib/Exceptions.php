<?php
    class ActionNotFoundException extends WaxException {
        function __construct($controller, $action) {
            parent::__construct("$controller::$action not found","$action was not found in Controller: $controller");
        }
    }
    class ControllerNotFoundException extends WaxException {
        function __construct($controller) {
            parent::__construct("$controller not found","Could not find controller: $controller");
        }
    }
?>
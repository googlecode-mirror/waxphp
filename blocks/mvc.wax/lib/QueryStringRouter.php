<?php
    class QueryStringRouter extends DCIObject {
        var $breakdown = array("controller","action");
        var $querystring;
        var $controller;
        var $action;
        var $args;
        
        function __construct() {
            $querystring = $this->querystring;
            
            if (is_null($querystring)) {
                $querystring = $_SERVER['QUERY_STRING'];
            }
            
            // if for some reason the querystring is a key in the $_GET array...
            // don't really remember why this was necessary
            if (isset($_GET[$querystring]))
				unset($_GET[$querystring]);
				
			$parts = explode("/",$querystring);
			foreach ($this->breakdown as $name) {
				$part = array_shift($parts);
				$_GET[$name] = $part;
				$_REQUEST[$name] = $_GET[$name];
				
			    $this->$name = $_GET[$name];
			}
			
			while ($part = array_shift($parts)) {
			    $pieces = explode(":",$part);
			    $_GET[$pieces[0]] = $pieces[1];
			    $_REQUEST[$pieces[0]] = $pieces[1];
			}
			
			// return remaining arguments-- excludes controller/action
			return $parts;
        }
        function GetTargetView() {
            $controller = $this->controller;
            $action = $this->action;
            
            return (empty($controller) ? "Default" : $controller) . "/" . (empty($action) ? "index" : $action);
        }
    }
?>
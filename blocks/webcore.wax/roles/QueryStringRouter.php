<?php
	interface rRouter {}
	interface rQueryStringRouter extends rRouter {}
	
	class rQueryStringRouterActions {
		var $controller;
		var $action;
		
		static function Route(rQueryStringRouter $self) {
			if (isset($_GET[$_SERVER["QUERY_STRING"]]))
				unset($_GET[$_SERVER["QUERY_STRING"]]);
			$parts = explode("/",$_SERVER["QUERY_STRING"]);
			
			$breakdown = array("controller","action");
			
			foreach ($breakdown as $name) {
				$_GET[$name] = array_shift($parts);
				$_REQUEST[$name] = $_GET[$name];
				$self->$name = $_GET[$name];
			}
			
			while (count($parts) > 0) {
				$pieces = explode(":",array_shift($parts));
				$_GET[$pieces[0]] = $pieces[1];
				$_REQUEST[$pieces[0]] = $pieces[1];
			}
		}
	}
?>
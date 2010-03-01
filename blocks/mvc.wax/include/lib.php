<?php
    /**
	* Generates a URL to a resource in the application
	*/
	function url_to($action = NULL, $controller = NULL, $args = NULL) {
	    $base = str_replace($_SERVER['QUERY_STRING'],'',$_SERVER['REQUEST_URI']);
	    $base = array($base);
	    
	    if (is_null($controller)) {
	        $xqs = explode("/",$_SERVER['QUERY_STRING']);
	        $base[] = array_shift($xqs);
	    }
	    else
	        $base[] = $controller;
	    
	    if (!is_null($action)) $base[] = $action;
	    
	    if (!is_null($args) && is_array($args)) {
	        foreach ($args as $key => $value) {
	            $base[] = "$key:$value";
	        }
	    }
	    
	    return str_replace("//","/",implode("/",$base));
	}
	
	function link_to($text, $action = NULL, $controller = NULL, $args = NULL, $attribs = array()) {
	    foreach ($attribs as $name => $value) {
	        $attribs[$name] = "$name='" . $value . "'";
	    }
	    return "<a href='" . url_to($action,$controller,$args) . "' " . implode(" ",$attribs) . ">" . $text . "</a>";
	}
	
	/**
    * HTTP Redirection Method
    * This method takes an action as an argument, then causes
    * execution to stop and 3 methods of redirection to be sent 
    * to the client:
    *    - HTTP Location Header
    *    - META tag refresh (failsafe in case the header fails)
    *    - Javascript location.href redirect
    * Note that this method does not allow redirection to 
    * a different controller.
    *
    * @param string $action The action to redirect to
    * @param array $args An array of additional arguments
    */
    function redirect($action, $controller = NULL, $args = NULL) {
        @ob_end_clean(); // stop output buffering and disregard any data
        
        if (is_null($controller)) {
            $arr = debug_backtrace();
            $call = array_shift($arr);
            $ctrller = array();
            preg_match_all("/(\w+)Ctx/",$call['file'],$ctrller);
            $controller = $ctrller[1][0];
        }
        $url = url_to($action, $controller, $args);
        header("Location: $url");
        echo "<meta http-equiv='refresh' content='2'>";
        echo "<script type='text/javascript'>location.href='$url';</script>";
        die();
    }
?>
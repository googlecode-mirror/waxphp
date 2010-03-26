<?php
    /**
	* Generates a URL to a resource in the application
	*/
	function url_to($action = NULL, $context = NULL, $args = NULL) {
	    $router = new QueryString();
	    
	    if (is_null($context)) {
            $router = new QueryString();
            $route = $router->Analyze($_SERVER['QUERY_STRING']);
            $context = $route['context'];
        }
        if (empty($context))
            $context = "Default";
	    
	    $route = array(
	        'context' => $context,
	        'action' => $action
        );
        if (is_array($args)) {
            foreach ($args as $arg => $val) {
                $route[$arg] = $val;
            }
        }
        
        $base = str_replace($_SERVER['QUERY_STRING'],'',$_SERVER['REQUEST_URI']);
        $qs = $router->Generate($route);
        
        return $base . $qs;
	}
	
	function link_to($text, $action = NULL, $context = NULL, $args = NULL, $attribs = array()) {
	    foreach ($attribs as $name => $value) {
	        $attribs[$name] = "$name='" . $value . "'";
	    }
	    return "<a href='" . url_to($action,$context,$args) . "' " . implode(" ",$attribs) . ">" . $text . "</a>";
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
    function redirect($action, $context = NULL, $args = NULL) {
        @ob_end_clean(); // stop output buffering and disregard any data
        
        $url = url_to($action, $context, $args);
        header("Location: $url");
        echo "<meta http-equiv='refresh' content='2'>";
        echo "<script type='text/javascript'>location.href='$url';</script>";
        die();
    }
?>
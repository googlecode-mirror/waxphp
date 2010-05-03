<?php
    /**
	* Generates a URL to a resource in the application
	*/
	function url_to($method = "index", $obj = NULL, array $args = array()) {
	    if (is_object($obj))
	        $obj = get_class($obj);
	    else if (is_null($obj)) {
	        $parts = explode("/",$_SERVER['QUERY_STRING']);
	        if (isset($parts[0]))
    	        $obj = $parts[0];
	    }
	    if (empty($obj))
	        $obj = "Home";
	        	        
	    $url = array($obj);
	    if (isset($args['id'])) {
	        $url[] = $args['id'];
	        unset($args['id']);
	    }
	    
	    $url[] = $method;
	    $url = array_merge($url,$args);
	    
	    $base = urldecode($_SERVER['REQUEST_URI']);
	    $base = str_replace($_SERVER['QUERY_STRING'],'',preg_replace("/\/+/","/",$base));
	    $base .= implode("/", $url);
	    
	    return $base;
	}
	
	function link_to($text, $method = NULL, $object = NULL, $args = array(), $attribs = array()) {
	    return "<a href='" . url_to($method, $object, $args) . "'>$text</a>";
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
    function redirect($method, $obj = NULL, $args = array()) {
        @ob_end_clean(); // stop output buffering and disregard any data
        
        $url = url_to($method, $obj, $args);
        header("Location: $url");
        
        echo "<meta http-equiv='refresh' content='2'>";
        echo "<script type='text/javascript'>location.href='$url';</script>";
        die();
        exit;
    }
    
    function render_view(WaxBlock $block, $viewname, $args = array()) {
        $view = new View($block, $viewname);
        $vrctx = new ViewRenderCtx();
        return $vrctx->Execute($view, $args);
    }
?>
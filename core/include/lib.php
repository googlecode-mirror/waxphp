<?php
	/**
	* Function to recursively include PHP files in a directory.
	* Uses require_once to ensure each file is included only
	* once.
	*
	* Warning: Do not use on deep directory structures.
	*
	* @param string $dir The directory to include
	*/
	function require_dir($dir) {
    	if (is_dir($dir)) {
    		$objects = scandir($dir);
        	foreach ($objects as $file) {
                if ($file[0] == ".") continue;
                else if (is_dir("$dir/$file")) {
                	require_dir("$dir/$file");
                }
                else if (strpos($file,".php") != false && $file[0] != "_") {
                	require_once("$dir/$file");
                }
            }
        }
    }
    
	/**
	* Generates a URL to a resource in the application
	*/
	function url_to($action = NULL, $controller = NULL, $args = NULL) {
	    $base = str_replace($_SERVER['QUERY_STRING'],'',$_SERVER['REQUEST_URI']);
	    $base = array($base);
	    
	    // try one last time to find the controller
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
            preg_match_all("/(\w+)Controller/",$call['file'],$ctrller);
            $controller = $ctrller[1][0];
        }
        $url = url_to($action, $controller, $args);
        header("Location: $url");
        echo "<meta http-equiv='refresh' content='2'>";
        echo "<script type='text/javascript'>location.href='$url';</script>";
        die();
    }
	/**
	* This allows for explicit role calling within a context,
	*
	* contexts can be defined and called inline in this fashion:
	* $ctxresult = _ctx(function (rRole $object, $arg = "value") use ($object) {
	*   // context code
	* });
    *
    * Note this uses PHP 5.3's lambda functions and closures to accomplish this.
	*/
	function _inlinectx($exfunc) {
	    // reflect the function to determine the casted types
	    $var = new ReflectionFunction($exfunc);
	    $params = $var->getParameters();

	    $fallbacks = array();
	    $types = array();
	    $args = array();
	    
	    // for each parameter to the function (the parts before 'use')
	    foreach ($params as $param) {
	        $str = $param->__toString();
	        $matches = array();
	        
	        // find out which role we're casting too for each var
	        // Note!: the regex looks for the 'r' prefix before a role name
	        preg_match_all("/\s+(r[a-z0-9_]+)\s+\\\$([a-z_][a-z0-9_]+)/i",$str,$matches);
	        
	        // foreach match (realistically 1 or 0 matches will occur)
            foreach ($matches[1] as $index => $role) {
                $varname = $matches[2][$index];         // the object we're going to be "casting"
                global $$varname;                       // bring the object into scope
                $obj = $$varname;                       // create a reference to it
                $fallbacks[$varname] = $obj->fallback;  // save its current fallback role
                $obj->fallback = $role;                 // update to the role we're casting to
                $args[] = $obj;                         // pass the object as an argument to the context
            }
	    }
	    
	    // now we can execute the context
	    $result = call_user_func_array($exfunc,$args);
	    
	    // undo the casting (put the object back to the way we found it)
	    foreach ($fallbacks as $varname => $fallback) {
	        $obj = $$varname;
	        $obj->fallback = $fallback;
	    }
	    
	    // and then return the result of the context call
	    return $result;
	}
	function _debug($obj) {
	    $trace = debug_backtrace();
	    $trace = array_shift($trace);
	    echo "<b>Debug</b> at " . $trace['file'] . ":" . $trace['line'] . "<br /><pre>" . print_r($obj,true) . "</pre><br />";
	}
	function _error($title, $message, $code = E_USER_NOTICE) {
        trigger_error($title . ": " . $message, $code);
    }
?>
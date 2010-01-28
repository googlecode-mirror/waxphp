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
    
    function wax_error($title, $message, $code = E_USER_NOTICE) {
        trigger_error($title . ": " . $message, $code);
    }
	
	/**
	* Gets a resource from a given block
	*
	* @param string $block The name of the block to get the resource from
	* @param string $resource_type The type of resource to get (view/image/js/css/etc...)
	* @param string $name The name of the resource to get
	*/
	function get_resource_from($block, $resource_type, $name) {
		$block = Wax::GetBlock($block);
		$item = $block->$resource_type($name);
		
		if (!empty($item))
			return $item;
		else return '';
	}
	
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
?>
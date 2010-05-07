<?php
    /**
    * The Wax PHP Framework helper utilities.  The functions in this file
    * are meant to be shortcuts to access certain functionality that is 
    * commonly used in the course of Wax development.
    *
    * @author Joe Chrzanowski
    * @version 0.11
    */
    
    
	/**
	* Function to recursively include PHP files in a directory.
	* Uses require_once to ensure each file is included only
	* once.
	*
	* Warning: Will only include up to $max_depth directories
	*
	* @param string $dir The directory to include
	* @param int $max_depth The maximum depth to include directories
	*/
	function _require_dir($dir, $max_depth = 10) {
	    if ($max_depth == 0) return;
	    
    	if (is_dir($dir)) {
    		$objects = scandir($dir);
        	foreach ($objects as $file) {
                if ($file[0] == ".") continue;
                else if (is_dir("$dir/$file")) {
                	require_dir("$dir/$file", $max_depth - 1);
                }
                else if (strpos($file,".php") != false && $file[0] != "_") {
                	require_once("$dir/$file");
                }
            }
        }
    }
    // provided for compatibility purposes:
    function require_dir($dir, $max_depth = 10) {
        _require_dir($dir, $max_depth);
    }
    
    /**
    * Echoes an object or variable as preformatted text.  Also
    * displays the file and line where the function is called
    *
    * @param mixed $obj The object to print
    */
	function _debug($obj) {
	    $trace = debug_backtrace();
	    $trace = array_shift($trace);
	    echo "<b>Debug</b> at " . $trace['file'] . ":" . $trace['line'] . "<br /><pre>" . print_r($obj,true) . "</pre><br />";
	}
	
	/**
	* Gets a resource out of the Wax runtime.
	*
	* @param mixed $block Either a WaxBlock or the name of the block that the resource should be retrieved from.
	* @param string $resource_type One of the WaxBlock resource types (js/lib/views/css/etc...)
	* @param string $resource_name The name of the resource to retrieve
	* @return string
	*/
	function _resource($block, $resource_type, $resource_name) {
	    if (!($block instanceof WaxBlock)) {
	        $block = BlockManager::GetBlock($block);
	    }   
	    $resource = $block->GetResource($resource_type, $resource_name);
	    return $resource;
	}
	
	/**
	* Translates a resource into a resource address for use in Wax apps
	*/
	function _resourceid($object, $method = NULL, array $args = array()) {
	    if (is_string($object)) {
	        $object = new $object();
	    }
	    if ($object instanceof WaxObject) {
	        $objname = get_class($object);
	        $objid = $object->id;
	        
	        $url = array($objname, $objid);
	        if (!is_null($method)) {
	            if (!isset($object->methods[$method]))
	                throw new InvalidResourceException($object, $method, $args);
	            else
	                $url[] = $method;
	        }
            $url = array_merge($url, $args);
            return implode("/",$url);
	    }
	    else {
	        throw new UnlinkableResourceException($object,$method,$args);
	    }
	}
	
	/**
	* Prints out Wax text
	*/
	function _wax() {
	    echo "<span style='font-weight:bold;'><a href='http://code.google.com/p/waxphp'>" . 
	         "<span style='color:#162d50;'>W</span>" . 
	         "<span style='color:#2c5aa0;'>A</span>" . 
	         "<span style='color:#87aade;'>X</span>" . 
	         "</a></span>";
	}
?>
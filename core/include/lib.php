<?php
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
	function require_dir($dir, $max_depth = 10) {
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
?>
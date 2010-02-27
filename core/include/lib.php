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
	function _debug($obj) {
	    $trace = debug_backtrace();
	    $trace = array_shift($trace);
	    echo "<b>Debug</b> at " . $trace['file'] . ":" . $trace['line'] . "<br /><pre>" . print_r($obj,true) . "</pre><br />";
	}
	function _error($title, $message, $code = E_USER_NOTICE) {
        trigger_error($title . ": " . $message, $code);
    }
?>
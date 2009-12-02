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
?>
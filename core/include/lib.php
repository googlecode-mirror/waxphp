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
	* Takes the name and parent block of a view and renders it.
	*
	* @param string $view The name of the view to render
	* @param array $arguments The list of arguments to pass to the view
	* @param string $blockname The parent block of the view to render
	* @param bool $return Whether or not to return the view results or just print them out
	* @return mixed if {@link $return} is set to true, returns the rendered view, otherwise returns nothing
	*/
	function render($view, $arguments, $blockname, $return = false) {
		$block = Wax::GetBlock($blockname);
		if (!is_null($block)) {
			$viewfile = $block->views($view);
			$r = new Renderer();
			$buf = $r->Render($viewfile,$arguments);
			if ($return) return $buf;
			else echo $buf;
		}
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
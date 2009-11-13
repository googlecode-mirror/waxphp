<?php
	// recursive require_once
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

	function render($view, $arguments, $blockname) {
		$block = Wax::GetBlock($blockname);
		if (!is_null($block)) {
			$viewfile = $block->views($view);
			$r = new Renderer();
			echo $r->Render($viewfile,$arguments);
		}
	}
	
	function get_resource_from($block, $resource_type, $name) {
		$block = Wax::GetBlock($block);
		$item = $block->$resource_type($name);
		
		if (!empty($item))
			return $item;
		else return '';
	}
?>
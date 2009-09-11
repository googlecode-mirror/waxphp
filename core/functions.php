<?php
	/**
	* This file contains a few utility functions that don't really belong within a class.
	* or need to be frequently referenced out of object context
	*
	* Three functions are used for getting resources from within a view:
	* 	- get_resource_from ($resource_type, $resource_name, $parent_block = NULL)
	*   - get_resource_at($wax_path, $arguments = NULL)
	*   - tag_for_resource($resource_type, $resource_name, $parent_block = NULL)
	*
	* There are a few other functions for just general work that come in handy:
	*   - require_dir -- recursively includes PHP files in a directory
	* 	- render -- render a view
	*	- link_to -- generate a link to a specific querystring from the current site
	*   - alink_to -- generate a querystring from the specified array
	*   - redirect_to -- send a http header, meta, and javascript redirect to the client
	*/

	// ie: get_resource_from("images","imgname");
	// or: get_resource_from("images","imgname","otherblock");
	function get_resource_from($type,$name,$block = NULL) {
		// shortcut function:
		if ($block == NULL) {
			$backtrace = debug_backtrace();
			$called_from = $backtrace[0]['file'];
			$block = BlockManager::GetBlocknameFromPath($called_from);
		}
		$block_obj = Wax::GetBlock($block);
		if (!is_null($block_obj)) {
			$resource_path = $block_obj->GetResource($type,$name);
			return $resource_path;
		}
		else {
			return null;
		}
	}
	// gets a resource located at a specific waxpath:
	// ie:
	// 	get_resource_at("fs/block/image",array("block" => "iphone","image" => "banner"));
	//  will return the image located in the iphone block
	function get_resource_at($path,$args = NULL) {
		$rpath = Wax::LookupPath($path,$args);
		return $rpath;		
	}
	
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

	// view rendering function
	function render($view,$args,$block = NULL) {
		// shortcut function:
		if ($block == NULL) {
			$backtrace = debug_backtrace();
			$called_from = $backtrace[0]['file'];
			$block = BlockManager::GetBlocknameFromPath($called_from);
		}
		$block_obj = Wax::GetBlock($block);
		if (!is_null($block_obj)) {
			$resource_path = $block_obj->GetResource("views",$view);
			if (file_exists($resource_path)) {
				$renderer = new Renderer();
				echo $renderer->Render($resource_path,$args);
			}
 			else throw new Exception("View not found: $view in $block");
		}
	}
	
	// navigation functions
	function link_to($args, $relative = true) {
		$base = ($relative ? $_GET : array());
		foreach ($args as $arg => $value) {
			if ($value == NULL && isset($base[$arg]))
				unset ($base[$arg]);
			else
				$base[$arg] = $value;
		}
		$res = array();
		foreach ($base as $arg => $value) {
			if (!empty($value)) {
				$res[] = "$value";
			}
		}
		$qs = implode("/",$res);
		
		return Wax::LookupPath("web/app") . "/" . $qs;
	}
	// absolute link to
	function alink_to($args) {
		return link_to($args,false);
	}
	function redirect_to($args, $relative = true) {
		$loc = link_to($args);
		header("Location: " . $loc);
		
		echo render(get_resource_from("views","redirect","default"),array("loc" => $loc));
		ob_flush();
	}
?>

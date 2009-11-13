<?php
	/**
	*  Base class for WaxBlocks -- can be used to build plugins/libraries/etc.
	*  This class performs all the required actions necessary to load and access
	*  information from a block
	*/
	class WaxBlock {
		private $_resources = array(
			'views' => array(),
			'js' => array(),
			'css' => array(),
			'images' => array(),
			'roles' => array()
		);
		var $name = NULL;
		private $_blockdir = '';
		
		function __construct($blockpath, $include_files = false) {
			$info = pathinfo($blockpath);
			$this->_blockdir = $info['dirname'] . "/" . $info['basename'];
			$this->name = BlockManager::GetBlocknameFromPath($this->_blockdir);
			$this->loadResources($blockpath, $include_files);
		}
				
		private function loadResources($dir, $include_files = false) {
			$allowed = array("roles","lib","views","js","css","images");
			foreach ($allowed as $file) {	
				if (is_dir("$dir/$file")) {
					if ($file == "lib") {
						require_dir("$dir/lib");
						continue;
					}
					foreach (scandir("$dir/$file") as $thisfile) {
						if ($thisfile[0] == '.' || $thisfile[0] == '_') continue;
						else {
							if ($file == "roles" && $include_files) {
								require_once("$dir/$file/$thisfile");
							}
							
							if ($file == "views") {
								$viewsdir = "$dir/$file/$thisfile";
								if (is_dir($viewsdir)) {
									foreach (scandir($viewsdir) as $viewfile) {
										if ($viewfile[0] == '.' || $viewfile[0] == '_') continue;
										$this->_resources[$file]["$thisfile/" . array_shift(explode(".",$viewfile))] = "$viewsdir/$viewfile";
									}
								}
								else $this->_resources[$file][array_shift(explode(".",$thisfile))] = $viewsdir;
							}
							else {
								$path = str_replace($_SERVER['DOCUMENT_ROOT'],'',($this->_blockdir . "/$file/$thisfile"));
								$this->_resources[$file][array_shift(explode(".",$thisfile))] = $path;
							}
						}
					}
				}
			}
			
			$files = @scandir($dir);
			$files = (!is_array($files) ? array() : $files);
			foreach ($files as $file) {
				if ($file[0] == '.') continue;			// the file is hidden
				else if ($file[0] == "_") continue;		// the file is disabled
				else if (is_dir("$dir/$file")) continue;
				else {
					$ext = array_pop(explode('.',$file));
					switch ($ext) {
						case "php":
							if ($include_files) {
								require_once($dir . '/' .$file);
							}
						break;
						default:
							// don't know... php files should be the only ones here
							break;
					}
				}
			}
		}
		
		function GetResources() {
			return $this->_resources;
		}
		
		function GetResource($type,$name) {
			if (isset($this->_resources[$type]) && isset($this->_resources[$type][$name])) {
				return $this->_resources[$type][$name];
			}
			else {
				return "Resource Not Found: $type/$name";
			}
		}
		
		function __call($func, $args) {
			// redirect to the proper array
			if (isset($this->_resources[$func])) {
				$arg = $args[0];
				return (isset($this->_resources[$func][$arg]) ? $this->_resources[$func][$arg] : NULL);
			}
			else return NULL;
		}
		function __get($var) {
			// return the array
			if (isset($this->_resources[$var]))
				return $this->_resources[$var];
		}
	}
?>

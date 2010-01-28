<?php
	/**
	*  Base class for WaxBlocks -- can be used to build plugins/libraries/etc.
	*  This class performs all the required actions necessary to load and access
	*  information from a block
	*/
	
	class ResourceNotFoundException extends WaxException {
		function __construct($resource, $block) {
			parent::__construct("Resource $resource not found in {$block->name}","<pre>" . print_r($block,true) . "</pre>");
		}
	}
	class ResourceNotFoundInException extends ResourceNotFoundException {
		function __construct($type, $resource, $block) {
			parent::__construct(
				"Resource $resource not found in {$block->name}/$type",
				print_r($block->GetResources(),true)
			);
		}
	}
	class BlockNotFoundException extends WaxException {
	    function __construct($path) {
	        parent::__construct("WaxBlock not found: $path","");
	    }
	}
	
	class WaxBlock extends ArraySurrogate {
		private $_resources = array(
			'views' => array(),
			'js' => array(),
			'css' => array(),
			'images' => array(),
			'roles' => array(),
			'blocks' => array()
		);
		var $name = NULL;
		private $_blockdir = '';
		
		function __construct($blockpath, $include_files = false) {
		    if (!is_dir($blockpath))
		        throw new BlockNotFoundException($blockpath);
		        
			$info = pathinfo($blockpath);
			$this->_blockdir = $info['dirname'] . "/" . $info['basename'];
			$this->name = $info['filename'];
			$this->loadResources($blockpath, $include_files);
			
			parent::__construct($this->_resources, true);
		}
		
		private function loadResources($dir, $include_files = false) {
			$dhtml = array("js","css","images");            // creates web-relative paths
			$php = array("blocks","roles","views","include","lib");  // creates fs-absolute paths
			
			foreach ($dhtml as $resourcedir) {
			    if (is_dir("$dir/$resourcedir")) {
			        foreach (scandir("$dir/$resourcedir") as $file) {
			            if ($file[0] == '.' || $file[0] == '_') continue;   // hidden and disabled files
			            else {
			                $path = str_replace($_SERVER['DOCUMENT_ROOT'],'',($this->_blockdir . "/$resourcedir/$file"));
			                $fileparts = explode(".",$file);
			                $title = array_shift($fileparts);
							$this->_resources[$resourcedir][$title] = $path;
			            }
			        }
			    }
			}
			
			foreach ($php as $resourcedir) {
			    if (is_dir("$dir/$resourcedir")) {
			        // these aren't resources, they're just meant for inclusion
			        // we still need to note their block context though
			        switch ($resourcedir) {
			            case "lib":
			            case "include":
			                require_dir("$dir/$resourcedir");
			            break;
			            
			            case "views":
                            foreach (scandir("$dir/$resourcedir") as $view) {
                                if ($view[0] == '.' || $view[0] == '_') continue;
                                
                                if (is_dir("$dir/$resourcedir/$view")) {
                                    foreach (scandir("$dir/$resourcedir/$view") as $subview) {
                                        if ($subview[0] == '.' || $subview[0] == '_') continue;
                                        
                                        $viewtitle = explode(".",$subview);
                                        $this->_resources["views"]["$view/" . array_shift($viewtitle)] = "$dir/$resourcedir/$view/$subview";
                                    }
                                }
                                else {
                                    $viewtitle = explode(".",$view);
                                    $this->_resources["views"][array_shift($viewtitle)] = "$dir/$resourcedir/$view";
                                }
                            }
                        break;
			            case "roles":
			                foreach (scandir("$dir/$resourcedir") as $role) {
			                    if ($role[0] == '.' || $role[0] == '_') continue;
			                    
			                    $rolename = explode(".",$role);
			                    $this->_resources["roles"][array_shift($rolename)] = "$dir/$resourcedir/$role";
			                    require_once "$dir/$resourcedir/$role";
			                }
			            break;
			            case "blocks":
			                foreach (scandir("$dir/$resourcedir") as $block) {
			                    if ($block[0] == '.') continue;
			                    
			                    $blockname = explode(".",$block);
			                    $this->_resources[$resourcedir][array_shift($blockname)] = BlockManager::LoadBlockAt("$dir/$resourcedir/$block");
			                }
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
				throw new ResourceNotFoundException("$type/$name",$this);
			}
		}
		
		function Tag($type, $name) {
		    $resource = $this->GetResource($type,$name);
		    
		    switch ($type) {
		        case "images":
		            return "<img src='$resource' />";
		        break;
		        case "js":
		            return "<script type='javascript' src='$resource'></script>";
		        break;
		        case "css":
		            return "<link rel='stylesheet' href='$resource' />";
		        break;
		        default:
                    return $resource;
		    }
		}
		
		function __call($func, $args) {
			// redirect to the proper array
			if (isset($this->_resources[$func])) {
				$arg = $args[0];
				if (isset($this->_resources[$func][$arg])) {
				    return $this->_resources[$func][$arg];
				}
				else throw new ResourceNotFoundInException($func,$arg,$this);
			}
			else throw new ResourceNotFoundException($func,$this);
		}
		function __get($var) {
			// return the array
			if (isset($this->_resources[$var]))
				return $this->_resources[$var];
		}
	}
?>

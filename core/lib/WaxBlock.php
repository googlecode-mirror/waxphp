<?php
	/**
	* Base class for WaxBlocks -- can be used to build plugins/libraries/etc.
	* This class performs all the required actions necessary to load and access
	* information from a block
	*
	* @author Joe Chrzanowski
	* @version 0.10
	*/
	class WaxBlock {
		var $_resources = array(
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
		}
		
		private function analyzeViewDir($dir, $viewprefix = '') {
		    foreach (scandir($dir) as $view) {
                if ($view[0] == '.' || $view[0] == '_') continue;
                
                if (is_dir("$dir/$view")) {
                    $append_vpfx = explode("/","$dir/$view");
                    $append_vpfx = array_pop($append_vpfx);
                    $new_viewprefix = ($viewprefix ? $viewprefix : "") . $append_vpfx . "/";
                    $this->analyzeViewDir("$dir/$view", $new_viewprefix);
                }
                else {
                    $viewtitle = explode(".",$view);
                    $this->_resources["views"][$viewprefix . array_shift($viewtitle)] = "$dir/$view";
                }
            }
		}
		
		private function loadResources($dir, $include_files = false) {
			$dhtml = array("js","css","images");                                // creates web-relative path refs
			$php = array("blocks","include","roles","views","lib","contexts");  // creates fs-absolute path refs
			
			// resource loading loop--
			// determines all javascript, css, and image resources
			// in the current block.
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
			
			// library loading loop--
			// determines all libraries, objects, roles, contexts, etc.
			// that need to be require()'d for this block to work like
			// it should.
			foreach ($php as $resourcedir) {
			    if (is_dir("$dir/$resourcedir")) {			        
			        switch ($resourcedir) {
			            // these are all treated the same -- 
			            // the only thing that matters is the order,
			            // which is specified in the $php array.
			            case "lib":
			            case "contexts":
			            case "include":
			                require_dir("$dir/$resourcedir");
			            break;
			            
			            case "views":
                            $this->analyzeViewDir("$dir/$resourcedir");
                        break;
                        
                        // the idea behind the blocks and roles directories
                        // is pretty much the same
                        case "blocks":
			            case "roles":
			                foreach (scandir("$dir/$resourcedir") as $obj) {
			                    if ($obj[0] == '.' || $obj[0] == '_') continue;
			                    $objname = explode(".",$obj);
			                    
			                    $key = array_shift($objname);
			                    $objval = "$dir/$resourcedir/$obj";
			                    
			                    if ($resourcedir == "roles")
			                        require_once("$dir/$resourcedir/$obj");
			                    else 
			                        $objval = BlockManager::LoadBlockAt("$dir/$resourcedir/$obj");
			                        
			                    $this->_resources[$resourcedir][$key] = $objval;
			                }
			            break;
			        }
				}
			}
		}

		function GetResource($type,$name) {
			if (isset($this->_resources[$type]) && isset($this->_resources[$type][$name])) {
				return $this->_resources[$type][$name];
			}
			else {
				throw new ResourceNotFoundException("$type/$name",$this);
			}
		}
		
		function GetBaseDir() { return $this->_blockdir; }
		
		function __call($func, $args) {
			$arg = array_shift($args);
			return $this->GetResource($func, $arg);
		}
		function __get($resourcetype) {
		    if (is_array($this->_resources[$resourcetype])) {
		        return new ArraySurrogate($this->_resources[$resourcetype]);
		    }
		    else throw new ResourceNotFoundException($resourcetype,$this);
		}
	}
	
	class ResourceNotFoundException extends WaxException {
		function __construct($resource, $block) {
			parent::__construct("$resource not found", "<pre>" . print_r($block->_resources['views'],true) . "</pre>");
		}
	}
	class BlockNotFoundException extends WaxException {
	    function __construct($path) {
	        parent::__construct("WaxBlock not found: $path","");
	    }
	}
?>

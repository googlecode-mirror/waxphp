<?php
	class BlockManager {
		/**
		* A list of resources that have been loaded by various blocks
		*/
		private static $_blockresources = array();
		private static $_pathcache = array();
		
		/**
		* Loads a given block
		* 
		* @param string $block The name of the block to load
		* @return The block object
		* @throws 
		*/
        static function LoadBlock($block) {
        	self::GetBlock($block);
        }
        
        // returns a block by name -- looks thru the blockpath
        // to try and find the block
        static function GetBlock($block) {
        	if (isset(Wax::$loaded_blocks[$block]))
        		return Wax::$loaded_blocks[$block];
        	else {
        	    try {
    	        	$path = self::findBlock($block);
    	        	return self::LoadBlockAt($path);
    	        }
    	        catch (BlockNotFoundException $e) {
    	            throw new BlockNotFoundException($block);
    	        }
	        }
        }
        
        static function LoadBlockAt($path = NULL) {
        	if (is_null($path) || empty($path)) 
				throw new BlockNotFoundException($path);
        	else {
        	    $blockobj = new WaxBlock($path);
        	    Wax::$loaded_blocks[$blockobj->name] = $blockobj;
        	    return $blockobj;
        	}
        }
        
        // used mostly for getting all resources needed for an application
        // usually in a header or something of the sort
        static function GetLoadedBlocks($nameonly = false) {
        	return ($nameonly ? array_keys(Wax::$loaded_blocks) : Wax::$loaded_blocks);
        }
        // find out which block a file is located in -- doesn't always work
        static function GetBlockFromContext($filename = NULL) {
			$classname = NULL;
			
			if (is_object($filename)) {
			    // look thru the backtrace
			    $bt = debug_backtrace();
			    $frame = array_pop($bt);
			    $filename = $frame['file'];
			}
			
			if (isset(self::$_pathcache[$filename]))
			    return self::$_pathcache[$filename];
			
			// parse out the block...
			if (file_exists($filename)) {
    			$info = pathinfo($filename);
    			$pathparts = array_reverse(explode("/",$info['dirname']));
    			
    			foreach ($pathparts as $part) {
    				if (strpos($part,".wax") !== false) {
    				    $parts = explode(".",$part);
    					$block = self::GetBlock(array_shift($parts));
    					if (!is_null($block)) {
    					    self::$_pathcache[$filename] = $block;
    						return $block;
    					}
    					else throw new BlockNotFoundException($info['dirname']);
    				}
    			}
    		}
    		
			self::$_pathcache[$filename] = NULL;
			return NULL;
        }
		static function GetBlocknameFromPath($path) {
			$matches = array();
			preg_match_all("/(\w+)\.wax/",$path,$matches);
			if (isset($matches[1][0])) {
				$blockname = $matches[1][0];
				$block = $blockname;
				return $block;
			}
			else return NULL;
		}

		// get all css or js from all loaded blocks -- for header printing mostly
		static function GetDHTMLResources($ret_only = NULL) {
			$ret = array();
			foreach (self::GetLoadedBlocks() as $name => $block) {
				foreach ($block['js'] as $script) {
					$ret['js'][] = $script;
				}
				foreach ($block['css'] as $css) {
					$ret['css'][] = $css;
				}
			}
			if ($ret_only == 'js' || $ret_only == 'css')
				return $ret[$ret_only];
			else
				return $ret;
		}
        
		static function Init() {
			// autoload blocks
            foreach (WaxConf::$autoload as $block) {
            	self::LoadBlock($block);
            }
		}
        
        // Private functions
		// the findBlock function is responsible for 
		// searching the $blockpath variable for a given block
        private static function findBlock($block) {
        	foreach (WaxConf::$blockpath as $path) {
        		$blockloc = PathManager::LookupPath($path,array("block" => $block));
				if (is_dir($blockloc)) {
					return $blockloc;
	        	}
	        }
			return NULL;
        }
	}
?>

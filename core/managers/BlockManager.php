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
	        	$path = self::findBlock($block);
	        	return self::LoadBlockAt($path);
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
			
			// if we're getting the block context of an object,
			// we need to backtrace to the file where the class
			// is declared and get the block from the path of that file.
			if (is_object($filename)) {
			    // look thru the backtrace
			    $bt = debug_backtrace();
			    $lookfor = get_class($filename);
			    
			    $filename = array_shift($bt);
			    while ($filename['file'] != $lookfor && count($bt) > 0)
			        $filename = array_shift($bt);
			        
			    $filename = $filename['file'];
			}
			
			// cache blocks located at certain locations
			if (isset(self::$_pathcache[$filename]))
			    return self::$_pathcache[$filename];
			
			// parse out the block from the filename (Looks for .wax extension)
			if (file_exists($filename)) {
    			$info = pathinfo($filename);
    			$pathparts = array_reverse(explode("/",$info['dirname']));
    			
    			foreach ($pathparts as $part) {
    				if (strpos($part,".wax") !== false) {
    				    // at this point we've found a path ending in .wax
    				    // which represents the deepest level block: 
    				    // example: /app.wax/blocks/someblock.wax/views/layout.view.php
    				    // will return the someblock.wax block rather than app.wax
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
        
		// get all css or js from all loaded blocks -- for header printing mostly
		static function GetDHTMLResources($ret_only = NULL) {
			$ret = array('js' => array(), 'css' => array());
			foreach (self::GetLoadedBlocks() as $name => $block) {
				foreach ($block->js as $script) {
					$ret['js'][] = $script;
				}
				foreach ($block->css as $css) {
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

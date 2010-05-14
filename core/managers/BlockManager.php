<?php
    /**
    * The BlockManager is responsible for maintaining a list of all
    * loaded blocks and providing a central cache of all loaded 
    * 
    * @author Joe Chrzanowski
    * @version 0.10
    */
	class BlockManager {
		/**
		* A list of resources that have been loaded by various blocks
		*/
		private static $_blockresources = array();
		private static $_pathcache = array();
		private static $_loaded_blocks = array();
		
		/**
		* Loads a given block
		* 
		* @param string $block The name of the block to load
		* @return The block object
		*/
        static function LoadBlock($block) {
        	self::GetBlock($block);
        }
        
        /**
        * Gets a block by name --
        * looks through the loaded block cache first, if it's
        * not found, it looks for the block in the blockpaths
        *
        * @param string $block The name of the block to load
        * @return WaxBlock
        */
        static function GetBlock($block) {
        	if (isset(self::$_loaded_blocks[$block]))
        		return self::$_loaded_blocks[$block];
        	else {
	        	$path = self::findBlock($block);
	        	return self::LoadBlockAt($path);
	        }
        }
        
        /**
        * Loads a block at a given directory.  If the 
        * directory does not contain a block, an exception
        * is thrown.
        * 
        * @param string $path The path of the block to load
        * @return WaxBlock 
        * @throws BlockNotFoundException
        */
        static function LoadBlockAt($path) {
        	if (is_null($path) || empty($path)) 
				throw new BlockNotFoundException($path);
        	else {
        	    $blockobj = new WaxBlock($path);
        	    self::$_loaded_blocks[$blockobj->name] = $blockobj;
        	    
        	    // cache this block's resources
        	    // this part here in essence aggregates all loaded block resources 
        	    // into a single cache, making lookups for views, js, and css easier.
        	    foreach ($blockobj->GetResourceList() as $resource_type => $resources) {
        	        if (!isset(self::$_blockresources[$resource_type]))
        	            self::$_blockresources[$resource_type] = array();
        	            
        	        foreach ($resources as $name => $resource) {
        	            if (!isset(self::$_blockresources[$resource_type][$name]))
        	                self::$_blockresources[$resource_type][$name] = array();
        	                
        	            array_unshift(self::$_blockresources[$resource_type][$name], $resource);
        	        }
        	    }
        	    
        	    return $blockobj;
        	}
        }
        
        /**
        * Finds a resource path based on its name.  This method will
        * look through the resource cache and return the most recently
        * loaded path for the given name.
        *
        * @param string $type The type of resource to lookup (views / images / css / js / etc...)
        * @param string $name The name of the resource to lookup
        */
        static function Lookup($type, $name = NULL) {
            if (isset(self::$_blockresources[$type])) {
                if (isset(self::$_blockresources[$type][$name])) {
                    return self::$_blockresources[$type][$name][0];
                }
                else if (is_null($name)) {
                    $ret = array();
                    foreach (self::$_blockresources[$type] as $name => $locations) {
                        $ret[$name] = $locations[0];
                    }
                    return $ret;
                }
            }
            throw new ResourceNotFoundException("$type/$name");
        }
        
        /**
        * Get a list of loaded blocks
        *
        * @param bool $nameonly Whether to return the names of the blocks instead of the WaxBlocks
        * @return mixed
        */
        static function GetLoadedBlocks($nameonly = false) {
        	return ($nameonly ? array_keys(self::$_loaded_blocks) : self::$_loaded_blocks);
        }
       
        /**
        * Gets a block from from the calling context
        * If the filename isn't passed, this method looks through
        * the backtrace to determine the calling block contexts.
        *
        * @param string $filename A resource file located within some block
        * @return WaxBlock
        */
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
        
        /**
        * Looks through loaded blocks for JavaScript and CSS resources,
        * and returns the respective URLs for each one.
        *
        * @param string $ret_only Specify whether to retrieve only js or only css: 'js' or 'css'
        * @return array;
        */
		static function GetDHTMLResources($ret_only = NULL) {
			return array(
			    'js' => self::Lookup("js"),
			    'css' => self::Lookup("css")
			);
		}
        
        /**
        * Look through the Config::autoload array and load the 
        * specified blocks
        * 
        * @return void
        */
		static function Init() {
			// autoload blocks
            foreach (WaxConf::$autoload as $block) {
            	self::LoadBlock($block);
            }
		}
        
        /**
        * Look through the blockpath and try to find a 
        * block with the specified name
        *
        * @param string $block The name of the block to find
        * @return string
        */
        private static function findBlock($block) {
        	foreach (WaxConf::$blockpath as $path) {
        	    $check = $path . "/" . $block . ".wax";
        	    if (is_dir($check)) return $check;
	        }
			return NULL;
        }
	}
?>

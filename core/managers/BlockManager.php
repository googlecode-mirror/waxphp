<?php
	class BlockManager {
		// block functions
        static function LoadBlock($block) {
        	if (!isset(Wax::$loaded_blocks[$block])) {
	        	$path = self::findBlock($block);
	        	if (!empty($path)) {
	        		$blockobj = new WaxBlock($path, true);
		        	Wax::$loaded_blocks[$block] = $blockobj;
	        	
		        	return $blockobj;
				}
				else {
					throw new Exception("Couldn't load block: $block");
					return NULL;
				}
	        }
	        else return self::GetBlock($block);
        }
        
        // returns a block by name -- looks thru the blockpath
        // to try and find the block
        static function GetBlock($block = NULL) {
        	if (is_null($block))
        		echo "Trying to get block in $block<br />";
        	
        	if (isset(Wax::$loaded_blocks[$block]))
        		return Wax::$loaded_blocks[$block];
        	else {
	        	$path = self::findBlock($block);
	        	if (is_null($path) || empty($path)) return NULL;
	        	else return new WaxBlock($path);
	        }
        }
        
        // used mostly for getting all resources needed for an application
        // usually in a header or something of the sort
        static function GetLoadedBlocks() {
        	return Wax::$loaded_blocks;
        }
        // find out which block a file is located in -- doesn't always work
        static function GetBlockContext($file) {
        	$path = pathinfo($file);
        	$path = pathinfo($path['dirname']);
        	return $path['filename'];
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

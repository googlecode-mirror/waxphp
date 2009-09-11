<?
    ////////////////////////////////////////////////////
    // Wax
    //
    // Copyright 2008-2009 (c) Joe Chrzanowski
    ////////////////////////////////////////////////////
    
    // Configurations
    
    // Be extremely careful with WaxConf objects.
    // Any paths that must be determined go through the WaxConf object
    // Therefore, you can modify the entire framework structure by 
    // modifying the path variables within the WaxConf object.
    
    require_once(dirname(__FILE__) . "/core/functions.php");
    require_dir(dirname(__FILE__) . "/core");

    class Wax {
		private static $_init = false;
        public static $loaded_blocks = array();

        function __construct() { throw new Exception("ERROR: You can't instantiate a WaxConf object"); }
        static function Version($as_number = true) {
        	$v = WaxConf::$version;
        	return $v['version'] . "." . $v['revision'] . "." . $v['build'];
        }

		// redirect the more common stuff to the managers
        static function LookupPath($path, $args = NULL) {
			return PathManager::LookupPath($path,$args);
		}
		static function LoadBlock($block) {
			return BlockManager::LoadBlock($block);
		}
		static function GetBlock($block = NULL) {
			return BlockManager::GetBlock($block);
		}
        
        // initialize Wax
        static function Init($dir) {
            if (!self::$_init) {
                WaxConf::$paths['fs'] = dirname(__FILE__);
                WaxConf::$paths['web'] = str_replace($_SERVER['DOCUMENT_ROOT'],'',dirname(__FILE__));
            	// pre-parse the paths array for fast path lookups
                PathManager::PreParse();
                
                // determine application path
                $dir = str_replace('\\','/',$dir);
                WaxConf::$paths['app'] = str_replace(self::LookupPath('fs/'),'',$dir);
                
                // require Wax core
                $dir = self::LookupPath('fs/core');
				if (is_dir($dir))
	                require_dir("$dir");
	                
				// perform block auto-loading operations
	            BlockManager::Init();
	            
	            // ready to go
                self::$_init = true;
            }
        }
    }
    
	// start up wax, yield to application
	session_start();
	ob_start();
    error_reporting(E_ALL);
    Wax::Init(getcwd());
?>

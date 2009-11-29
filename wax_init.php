<?
    ////////////////////////////////////////////////////
    // Wax
    //
    // Copyright 2008-2009 (c) Joe Chrzanowski
    ////////////////////////////////////////////////////
    
    // Framework Configuration -- DO NOT EDIT!!!
    
    require_once(dirname(__FILE__) . "/core/include/lib.php");
    require_dir(dirname(__FILE__) . "/core/include");
    require_dir(dirname(__FILE__) . "/core/managers");
    require_dir(dirname(__FILE__) . "/core/lib");
    
    class Wax {
		private static $_init = false;				// boolean value specifying whether the framework has been initialized
        public static $loaded_blocks = array();		// maintain a list of loaded blocks for caching and efficiency

        function __construct() { 
            throw new WaxException("ERROR: You can't instantiate a WaxConf object"); 
        }
        
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
		
		static function RegisterRouter(rRouter $router) {
			self::$router = $router;
		}
        
        static function Init($dir) {
			global $argv, $argc;
			
            if (!self::$_init) {
                set_error_handler("wax_error_handler");
				set_exception_handler("wax_exception_handler");
				
                WaxConf::$paths['wax'] = str_replace($_SERVER['DOCUMENT_ROOT'],'',dirname(__FILE__));
				WaxConf::$paths['app'] = str_replace($_SERVER['DOCUMENT_ROOT'],'',$dir);
				
            	// pre-parse the paths array for fast path lookups
                PathManager::PreParse();
                
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
	@session_start();		// start up a session
	//ob_start();				// enable output buffering
    error_reporting(E_ALL); // enable error reporting
    Wax::Init(getcwd());	// init wax from the current working directory (which is presumably where the app is)
?>

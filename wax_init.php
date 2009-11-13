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
    
    require_once(dirname(__FILE__) . "/core/lib.php");
    require_dir(dirname(__FILE__) . "/core");	// special function for including a whole directory recursively

    class Wax {
		private static $_init = false;				// boolean value specifying whether the framework has been initialized
		
		public static $router = NULL;
        public static $loaded_blocks = array();		// maintain a list of loaded blocks for caching and efficiency

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
		
		static function RegisterRouter(rRouter $router) {
			self::$router = $router;
		}
        
        // initialize Wax
		// the droptoshell option, when set to true, will drop
		// the user into a PHP shell, where PHP commands as well
		// as couchDB queries can be executed manually.
        static function Init($dir) {
			global $argv, $argc;
			
            if (!self::$_init) {
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
	ob_start();				// enable output buffering
    error_reporting(E_ALL); // enable error reporting
    Wax::Init(getcwd());	// init wax from the current working directory (which is presumably where the app is)
?>

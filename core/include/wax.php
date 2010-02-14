<?php
    class Wax {
		private static $_init = false;				// boolean value specifying whether the framework has been initialized
        public static $loaded_blocks = array();		// maintain a list of loaded blocks for caching and efficiency

        function __construct() { 
            throw new WaxException("ERROR: You can't instantiate a WaxConf object"); 
        }
        
        static function Version() {
            return implode(".",WaxConf::$version);
        }
        
        static function Init($dir) {
			global $argv, $argc;
			
            if (!self::$_init) {
				set_exception_handler("wax_exception_handler");
				
                WaxConf::$paths['wax'] = str_replace(array($_SERVER['DOCUMENT_ROOT'],"/core/include"),'',dirname(__FILE__));
				WaxConf::$paths['app'] = str_replace($_SERVER['DOCUMENT_ROOT'],'',$dir);
				
            	// pre-parse the paths array for fast path lookups
                PathManager::PreParse();
                
                // require Wax core
                $dir = PathManager::LookupPath('fs/core');
				if (is_dir($dir))
	                require_dir("$dir");
	                
				// perform block auto-loading operations
	            BlockManager::Init();
	            
	            // ready to go
                self::$_init = true;
            }
        }
    }
?>
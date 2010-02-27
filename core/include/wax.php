<?php
    class Wax {
        function __construct() { 
            $bt = debug_backtrace();
            $last = array_shift($bt);
            self::Init(dirname($last['file']));
        }
        
        static function Version() {
            return implode(".",WaxConf::$version);
        }
        
        // Some aliases to the Managers
        static function LoadBlock($block) { BlockManager::LoadBlock($block); }
        static function GetBlock($block) { return BlockManager::GetBlock($block); }
        static function LoadBlockAt($path) { return BlockManager::LoadBlockAt($path); }
        
        static function RegisterDS($ds) { DataSourceManager::Register($name, $obj); }
        static function DataSource($name = NULL) { return DataSourceManager::Get($name); }
        ////////////////////////////////
        
        static function Init($dir) {
            if (!WaxConf::$init) {
				set_exception_handler("wax_exception_handler");
				
				// register block directories
				WaxConf::BlocksAt($dir . "/blocks");
				WaxConf::BlocksAt(__DIR__ . "/../../blocks");
                
                // require Wax core
                $dir = dirname(__FILE__) . "/..";
				if (is_dir($dir))
	                require_dir("$dir");
	                
				// perform block auto-loading operations
	            BlockManager::Init();
	            
	            // ready to go
                WaxConf::$init = true;
            }
        }
    }
?>
<?php
	class WaxConf {
		/**
		* Whether or not to enable global debug mode
		*/
		public static $debug = false;
		
		/**
		* Whether or not the framework has been initialized via call to Wax::Init()
		*/
        public static $init = false;
        
		/**
		* The version, revision, and current build of the framework
		*/
        public static $version = array(
        	"version"	=> 0,	// major release version
        	"revision"  => 10,	// minor version
        	"build" 	=> 0	// update number
        );
        
        /**
        * Paths to look for blocks.  This is auto-initialized in
        * Wax::Init(), however, you can add custom paths as well.
        */
        public static $blockpath = array(
        );
        
        /** 
        * the central database configuration list. 
        **/
        public static $database = array(
            'mysql' => array(
                'default' => array(
                    'dsn' => 'mysql:host=127.0.0.1; dbname=wax',
                    'username' => 'root',
                    'password' => '',
                )
            ),
            'sqlite' => array(
                'default' => array(
                    'dsn' => 'sqlite:wax_sqlite.sqlite',
                    'username' => '',
                    'password' => ''
                )
            )
        );
        
        /**
		* blocks to autoload
		*/
        public static $autoload = array(
        	"mvc",		// base libraries for web development
        	"database"
        );
		
		/**
		* Information about the Wax runtime
		*/
		public static function Info() { 
			return array(
				"version" => self::$version,
				"autoload" => self::$blockpath
			); 
		}
		
		/**
		* Register blocks in a directory
		*/
		public static function BlocksAt($path) {
		    $path = realpath($path);
		    if (array_search($path,self::$blockpath) === false) {
		        self::$blockpath[] = $path;
		    }		
		}
	}
?>

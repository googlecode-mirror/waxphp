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
        	"revision"  => 9,	// minor version
        	"build" 	=> 3	// update number
        );
        

		/**
		* This array defines the structure of the entire framework.  Changing these paths
		* without understanding how they work will cause serious issues when locating 
		* resources.  
		*
		* <ul>
		* <li><b>[varname]</b>: take the value of another value from $paths</li>
		* <li><b>{varname}</b>: take the value of an argument</li>
		* <li><b>&lt;varname&gt;</b>: a value from PHP's $_SERVER array</li>
		* </ul>
		* 
		* @todo Further document the path resolution system
		*/
        public static $paths = array(
        	// important base paths needed to determine working directories, etc..
            'fs' 			=> '<DOCUMENT_ROOT>',
			'web'			=> '',
			'ver'			=> '1.0',

			'wax'			=> '', // set @ runtime
            'app'			=> '', // set @ runtime
            'core' 			=> 'core',                                          // Folder that holds the core Wax functionality
                        
            // specify folder names for different types of data
            'blockdir' 		=> 'blocks',
            'imagedir' 	 	=> 'images',
            'scriptdir'		=> 'js',
            'cssdir' 		=> 'css',
            'viewdir' 		=> 'views',
            'roledir'		=> 'roles',
			'libdir' 		=> 'lib',
            
            // specify naming for each of the parts
            'appblock'		=> '{block}.wax',
            'block'			=> '[blockdir]/{block}.wax',
            'image' 		=> '[imagedir]/{image}',
			'lib'			=> '[libdir]/{lib}',
            'script'		=> '[scriptdir]/{script}.js',
            'css'			=> '[cssdir]/{css}.css',
            'role'			=> '[roledir]/{role}.php',
            'view'			=> '[viewdir]/{view}.view.php',
            
            // naming shortcuts for blocks - these look kind of weird
            // the purpose is to allow for easier name resolution by using the 
            // folder name in the block as the variable instead of the singlular 
            // version of whatever we're looking for
            'roles'			=> '[roledir]/{roles}.php',
            'images'		=> '[imagedir]/{images}',
            'js' 			=> '[scriptdir]/{js}.js'
        );
        
		/**
		* paths to look for blocks -- the order matters in this case
		* we want to look for blocks bundled with the application first,
		* then go on to the system's installed blocks
		*/
        public static $blockpath = array(
        	"fs/app/appblock",				// /app/Block.wax
    		"fs/app/block",					// /app/blocks/Block.wax
    		"fs/wax/block",					// /wax/blocks/Block.wax
			"fs/wax/blockdir/ver/appblock"	// /wax/blocks/1.0/Block.wax
        );
        
        /** 
        * the central database configuration list. 
        **/
        public static $database = array(
            "default_mysql" => array(
                'username' => 'root',
                'password' => '',
                'host' => "127.0.0.1",
                'db' => 'wax'
            )
        );
        
        /**
		* blocks to autoload (plugins/libraries/themes)
		*/
        public static $autoload = array(
        	"mvc",		// base libraries for web development
        );

		/**
		* Essentially the active theme -- this defines the default
		* resource block for the current application
		*/
		static $defaultResourceBlock = "resources";
		
		/**
		* Information about the Wax runtime
		*/
		public static function Info() { 
			return array(
				"version" => self::$version,
				"paths" => self::$paths,
				"blockpath" => self::$blockpath,
				"autoload" => self::$blockpath
			); 
		}
	}
?>

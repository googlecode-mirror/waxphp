<?php
	class WaxConf {
		public static $debug = false;
        public static $init = false;
        
        public static $version = array(
        	"version"	=> 0,
        	"revision"  => 7,
        	"build" 	=> 1
        );
        
        // Naming for the paths:
        // [varname]        a variable to be set at initialization time (another $paths value)
        // {varname}        a variable to be set at call-time
        // <varname>        a $_SERVER variable (set at initialization time)
        
        // DON'T CHANGE THESE UNLESS YOU KNOW WHAT YOU'RE DOING
        // You most likely should never ever have to touch this.
		// seriously-- if you mess this up, wax won't be able to find
		// any resources even if you tell it exactly where they are.
        public static $paths = array(
        	// important base paths needed to determine working directories, etc..
            'web'			=> '[relpath]',
            'DOCUMENT_ROOT' => '<DOCUMENT_ROOT>',
            'fs' 			=> '<DOCUMENT_ROOT>[relpath]',
            'app'			=> '',
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
        
        // paths to look for blocks
        public static $blockpath = array(
        	"fs/app/appblock",		// /app/Block.wax
    		"fs/app/block",		// /app/blocks/Block.wax
    		"fs/block",			// /wax/blocks/Block.wax
        );
        
        // blocks to autoload (plugins/libraries/themes)
        public static $autoload = array(
        	"mvc",		// allows for MVC based web development
        	"default"   // default HTML themes for wax
        );
	}
?>

<?
    ////////////////////////////////////////////////////
    // Wax
    //
    // Copyright 2008-2009 (c) Joe Chrzanowski
    ////////////////////////////////////////////////////
    
    // Framework Configuration -- DO NOT EDIT!!!
    if (defined("WAX_LOADED")) {
        die("ERROR: Wax is being loaded again from " . getcwd());
    }
    
    define("WAX_LOADED",true);
    
    require_once(dirname(__FILE__) . "/include/lib.php");
    require_dir(dirname(__FILE__) . "/include");
    require_dir(dirname(__FILE__) . "/managers");
    require_dir(dirname(__FILE__) . "/lib");
    
	// start up wax, yield to application
	@session_start();		// start up a session
	ob_start();
    error_reporting(E_ALL); // enable error reporting
    Wax::Init(getcwd());	// init wax from the current working directory (which is presumably where the app is)
?>

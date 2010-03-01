<?
    /** 
    * Wax Initialization Script
    *
    * Copyright 2008-2010 (c) Joe Chrzanowski
    *
    *
    * @author Joe Chrzanowski
    * @version 0.10
    */
    
    if (defined("WAX_LOADED")) {
        die("ERROR: Wax is being loaded again from " . getcwd() . ".<br />Previously loaded from: " . WAX_LOADED);
    }
    
    require_once(dirname(__FILE__) . "/include/lib.php");
    require_dir(dirname(__FILE__) . "/include");
    require_dir(dirname(__FILE__) . "/managers");
    require_dir(dirname(__FILE__) . "/lib");
    
	ob_start();
    error_reporting(E_ALL);
    Wax::Init(getcwd());
    
    define("WAX_LOADED",getcwd());
?>

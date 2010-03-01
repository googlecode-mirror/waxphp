<?php
	/**
	* Designed to hold information about the PHP $_FILES array
	* This class disables ArraySurrogate's Set function
	*
	* @author Joe Chrzanowski
	*/
	class FilesArr extends ArraySurrogate {
		function Set($index, $val) {
		    // the files array is read-only
			return;
		}
	}
	
	/**
	* Designed to hold $_COOKIE information.  Overries Set() and offsetUnset() functions
	* to apply to the actual $_COOKIE array.
	* 
	* @author Joe Chrzanowski
	*/
	class CookiesArr extends ArraySurrogate {
		function Set($index, $val) {
			setcookie($index,$val,0);		// create the cookie
		}
		function offsetUnset($offset) {
			setcookie($offset,"",time()-12000); // expire the cookie
		}
	}
	
	/**
	* Designed to hold $_SESSION information.  Overrides the __construct function
	* to ensure a session has been started.
	* 
	* @author Joe Chrzanowski
	*/
	class SessionArr extends ArraySurrogate {
		function __construct($parent) {
		    @session_start();
			parent::__construct($parent); 
		}
	}
?>
<?php
	class FilesArr extends ArraySurrogate {
		function Set($index, $val) {
			return;
		}
	}
	class CookiesArr extends ArraySurrogate {
		function Set($index, $val) {
			setcookie($index,$val,0);		// create the cookie
		}
		function offsetUnset($offset) {
			setcookie($offset,"",time()-12000); // expire the cookie
		}
	}
	class SessionArr extends ArraySurrogate {
		function __construct($parent) {
			parent::__construct($parent);
			@session_start();
		}
		function Set($index,$val) {
			session_register($index,$val);
			parent::Set($index,$val);
		}
	}
?>
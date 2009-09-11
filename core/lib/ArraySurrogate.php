<?php
    /**
    * The ArraySurrogate class is a class that allows this particular object
    * to act as a surrogate to an array.  This allows custom Get/Set methods to be
    * defined as necessary, like in the case of the $_COOKIE array.
    *
    * The $_COOKIE array is just an array, so to set/unset values, we need to call
    * the setcookie function.  Instead, we can extends the ArraySurrogate class, reimplement
    * the Get/Set functions, and we then have direct access to the $_COOKIE array via
    *
    * $cookie_surrogate['cookie_name'] = "cookie_value";
    *
    * @author Joe Chrzanowski
    */
	class ArraySurrogate implements ArrayAccess {
		private $_parent = NULL;
		function __construct($parent) { 
			$this->_parent =& $parent; 
		}
		
		// basic get and set methods
		function Get($index) {
			return $this->_parent[$index];
		}
		function Set($index,$val) {
			$this->_parent[$index] = $val;
		}
		
		// arrayAccess implementation
		function offsetExists($offset) {
			return isset($this->_parent[$offset]);
		}
		function offsetGet($offset) {
			return $this->Get($offset);
		}
		function offsetSet($offset,$value) {
			$this->Set($offset,$value);
		}
		function offsetUnset($offset) {
			unset($this->_parent[$offset]);
		}
		
		
		function __toString() {
			return "<pre>" . print_r($this->_parent,true) . "</pre>";
		}
	}
?>
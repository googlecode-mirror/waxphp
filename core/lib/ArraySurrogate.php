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
		protected $_arrayref = NULL;
		function __construct($parent = null, $reference = true) { 			
			if (!is_null($parent) && $reference)
				$this->_arrayref =& $parent; 
			else if (!is_null($parent))
				$this->_arrayref = $parent;
			else
				$this->_arrayref = array();
		}
		
		// basic get and set methods
		function Get($index) {
			return $this->_arrayref[$index];
		}
		function Set($index,$val) {
			$this->_arrayref[$index] = $val;
		}
		
		// arrayAccess implementation
		function offsetExists($offset) {
			return isset($this->_arrayref[$offset]);
		}
		function offsetGet($offset) {
			return $this->Get($offset);
		}
		function offsetSet($offset,$value) {
			$this->Set($offset,$value);
		}
		function offsetUnset($offset) {
			unset($this->_arrayref[$offset]);
		}
		
		function ToArray() {
			return $this->_arrayref;
		}
		
		function __toString() {
			return "<pre>\n" . print_r($this->_arrayref,true) . "</pre>\n";
		}
	}
?>
<?php
    /**
    * The ArraySurrogate class is a class that allows this particular object
    * to act as a surrogate to an array.  This allows custom Get/Set methods to be
    * defined as necessary.
    *
    * @author Joe Chrzanowski
    * @version 0.10
    */
	class ArraySurrogate implements ArrayAccess, Iterator {
	    /**
	    * A reference to the actual array
	    */
		protected $_arrayref = NULL;
		
		/**
		* Construct an ArraySurrogate
		*
		* @param array $parent The base array to use for this object
		* @param bool $reference Whether it should store a reference to the array or a copy
		*/
		function __construct($parent = null, $reference = true) { 			
			if (!is_null($parent) && $reference)
				$this->_arrayref =& $parent; 
			else if (!is_null($parent))
				$this->_arrayref = $parent;
			else
				$this->_arrayref = array();
		}
		
		function Get($index) {
		    if (array_key_exists($index,$this->_arrayref))
			    return $this->_arrayref[$index];
			else 
			    return NULL;
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
		
		// iterator implementation
        function rewind() {
            return reset($this->_arrayref);
        }
        function current() {
            return current($this->_arrayref);
        }
        function key() {
            return key($this->_arrayref);
        }
        function next() {
            return next($this->_arrayref);
        }
        function valid() {
            return key($this->_arrayref) !== null;
        }
		
		// returns the array
		function ToArray() {
			return $this->_arrayref;
		}
		
		// print_r()'s the array
		function __toString() {
			return "<pre>\n" . print_r($this->_arrayref,true) . "</pre>\n";
		}
	}
?>
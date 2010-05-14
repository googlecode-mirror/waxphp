<?php
	class WaxException extends DCIException {}
	
	class KeyNotFoundException extends WaxException {
	    function __construct($key,$arr) {
	        parent::__construct("Key not found","Key: '$key' not found in: <pre>" . print_r($arr,true) . "</pre>");
	    }
	}
	
	class ContextActionNotFoundException extends WaxException {
	    function __construct($context, $action) {
	        parent::__construct("$action not found","$action was not found in context $context");
	    }
	}               
	/**
	* Thrown when a role method is called that doesn't exist.
	*/
	class MethodNotFoundException extends DCIException {
		function __construct($method,$methods) {
			parent::__construct("$method not found","Role method '$method' not found in<pre>" . print_r($methods,true) . "</pre>");
		}
	}
	/**
	* Called when a property is gotten/set but has not been
	* defined by any implemented role
	*/
	class PropertyNotFoundException extends DCIException {
		function __construct($property, $properties) {
			parent::__construct("$property not found","Role property '$property' not found in<pre>" . print_r($properties,true) . "</pre>");
		}
	}
	/**
	* Called when a property is gotten/set but has not been
	* defined by any implemented role
	*/
	class RoleNotFoundException extends DCIException {
		function __construct($role) {
			parent::__construct("Role not found","Role '$role' not found");
		}
	}
	/**
	* Called when a property is defined in multiple roles
	*/
	class AmbiguousPropertyException extends DCIException {
	    var $implemented_in;
	    var $property;
	    function __construct($property,$implemented_in) {
	        $this->implemented_in = $implemented_in;
	        $this->property = $property;
	        parent::__construct("Ambiguous Property: $property","Property $property found in multiple roles: <pre>" . print_r($implemented_in,true) . "</pre>");
	    }
	}
	/**
	* Called when a property is defined in multiple roles
	*/
	class AmbiguousMethodException extends DCIException {
	    var $implemented_in;
	    var $method;
	    function __construct($method,$implemented_in) {
	        $this->implemented_in = $implemented_in;
	        $this->method = $method;
	        parent::__construct("Ambiguous Method: $method","Method $method found in multiple roles: <pre>" . print_r($implemented_in,true) . "</pre>");
	    }
	}
	
	class ContextNotFoundException extends WaxException {
        function __construct($ctx,$grpname) {
            parent::__construct("Context: $ctx Not Found","Could not execute $ctx in Context Group: " . $grpname);
        }
    }
    
    class AttributeNotFoundException extends WaxException {
        function __construct($attr, $obj) {
            parent::__construct("Attribute Not Found: $attr","$attr not found in " . get_class($obj) . "<pre>" . print_r($obj,true) . "</pre>");
        }
    }
    
    class ResourceNotFoundException extends WaxException {
		function __construct($resource, $block = NULL) {
		    if (is_null($block))
    			parent::__construct("$resource not found","The resource: $resource could not be found in any blocks.");
    		else
    		    parent::__construct("$resource not found", "<pre>" . print_r($block->_resources['views'],true) . "</pre>");
		}
	}
	class BlockNotFoundException extends WaxException {
	    function __construct($path) {
	        parent::__construct("WaxBlock not found: $path","");
	    }
	}
?>
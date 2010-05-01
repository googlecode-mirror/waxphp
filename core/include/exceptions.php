<?php
	function wax_error_handler($code, $message, $file, $line) {
        @ob_end_flush();
	    switch ($code) {
	        case E_NOTICE:
	        break;
	        case E_USER_NOTICE:
	            echo "<span class='wax_notice'>Wax Error: $message @ $file:$line</span><br />";
	        break;
	        default:
        	    throw new WaxException("Uncaught Error ($code)",$message, $code, $file, $line);
	    }
	}
	
	function wax_exception_handler($exception) {
	    @ob_end_flush();
		try {
		    if (!($exception instanceof WaxException)) {
		        // translate the exception into a wax exception
		        $exception = new WaxException($exception->getMessage(), "", $exception->getFile(), $exception->getLine());
		    }
			echo($exception->__toString());
		}
		catch (Exception $e) {
			echo (get_class($e) . " thrown within the exception handler. <br />" .
			    $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine() . "<br />");
		}
	}
	
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
    
    class InvalidResourceException extends WaxException {
        function __construct($obj, $method, $args) {
            parent::__construct("Invalid Resource Action Specified","Resource " . get_class($obj) . "." . $obj->id . " does not contain '$method'");
        }
    }
    class UnlinkableResourceException extends WaxException {
        function __construct($obj, $method, $args) {
            parent::__construct("Unlinkable Resource (Type = " . (is_object($obj) ? get_class($obj) : $obj) . ")", "Only WaxObject resources can be linked by URL.");
        }
    }
    
    class AttributeNotFoundException extends WaxException {
        function __construct($attr, $obj) {
            parent::__construct("Attribute Not Found: $attr","$attr not found in " . get_class($obj) . "<pre>" . print_r($obj,true) . "</pre>");
        }
    }
?>
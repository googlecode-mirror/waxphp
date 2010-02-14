<?php
	/**
	* The base class for Wax exceptions.  
	* 
	* This class is flexible to allow the display of exceptions 
	* depending on which blocks have been loaded.  By default looks
	* for a view named 'exception.view.php' in the resources block
	* or in the block set to WaxConf::$defaultResourceBlock
	*
	* @author Joe Chrzanowski <joechrz@gmail.com>
	*/
	class WaxException extends Exception {
    		protected $title = "";
    		protected $details = "";
    		protected $code = 0;
    		protected $file = '';
    		protected $line = 0;
    		
    		protected $view = array();
    		protected $viewfile = '';

    		/**
    		* Default exception constructor.  Receives the title and a textual
    		* representation of the details to display
    		*
    		* @param string $title The short description of the exception
    		* @param mixed $details The long description or an array to display
    		* @param int $code The error code for this exception
    		*/
    		function __construct($title, $details = "", $code = NULL, $file = NULL, $line = NULL) {
    			$this->title = $title;
    			$this->details = $details;
    			$this->code = $code;

    			$this->file = (is_null($file) ? $this->getFile() : $file);
    			$this->line = (is_null($line) ? $this->getLine() : $line);
    		}

    		/**
    		* This function returns the exception in a well-organized and readable way.
    		* Uses the 'exception' view from the $defaultResourceBlock.  If the view
    		* isn't found, it simply returns a textual representation
    		*/
    		function __toString() {
    			$msgbuf = $this->title . "<br /><i>" . $this->details . "</i><br /><br />";
				$msgbuf .= "An uncaught exception has occurred in: <br />";
				$msgbuf .= "<pre>";
				$trace = $this->getTrace();

                $tracelines = array();
                /* Ignore the files that handle function redirection- they throw the exceptions but they're irrelevant here. */
                if (strpos($this->getFile(),'DCIObject') === false && strpos($this->getFile(),'WaxBlock') === false)
				    $tracelines[] = $this->getFile() . ":" . $this->getLine();
				    
				foreach ($trace as $tr) {
				    if (isset($tr['file']) && (1 || (
				        strpos($tr['file'],'DCIObject') === false && 
				        strpos($tr['file'],'WaxBlock') === false
				    ))) {
				        $tracelines[] = $tr['file'] . ":" . $tr['line'];
				    }
				}
				$tracelines[0] = "<b>$tracelines[0]</b>";
				$msgbuf .= implode("<br />",$tracelines);

			    $msgbuf .= "</pre>";
			    return $msgbuf; 
    		}
    	}
	
	function wax_error_handler($code, $message, $file, $line) {
        @ob_end_flush();
	    switch ($code) {
	        case E_NOTICE:
	        break;
	        case E_USER_NOTICE:
	            echo "<span class='wax_notice'>WAX Error: $message @ $file:$line</span><br />";
	        break;
	        default:
        	    throw new WaxException("Uncaught Error ($code)",$message, $code, $file, $line);
	    }
	}
	
	function wax_exception_handler($exception) {
	    @ob_end_flush();
		try {
		    if (!($exception instanceof WaxException)) {
		        $exception = new WaxException($exception->getMessage(), "", $exception->getFile(), $exception->getLine());
		    }
			echo($exception->__toString());
		}
		catch (Exception $e) {
			echo (get_class($e) . " thrown within the exception handler. <br />" .
			    $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine() . "<br />");
		}
	}
	
	class DCIException extends WaxException {}
	
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
?>
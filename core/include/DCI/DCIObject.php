<?php
	/**
	* The DCIObject is any object that can perform roles
	*
	* This class is responsible for performing the background work
	* required for having role-playing objects, which includes
	* reflection, static context redirection, and determining if
	* a role or role action can be played/performed
	*/
	
	/**
	* The base DCIObject class
	*
	* This class provides base functionality for the rest of the framework.
	* The DCIObject class is meant to be extended and as a result provides
	* the ability to implement role methods, properties, and performs all 
	* necessary redirection to fully implement DCI
	*
	* @author Joe Chrzanowski <joechrz@gmail.com>
	* @version 2.0
	*/
	class DCIObject {
		/**
        * Shortcut to non-ambiguous methods
        */
		var $methods = array();
		
		/**
		* Shortcuts to non-ambiguous properties
		*/
		var $properties = array();
		
		/**
		* List of all roles and their methods and properties
		*/
		var $roles = array();
		
		/**
		* the $fallback variable solves problems with conflicting methods and
		* properties by defining a default role to use.  Every time a new role
		* is added, this variable is updated.  Note that this especially useful
		* in the case of an _inline_context (defined in /wax/core/include/lib.php):
		*
		* _inline_context(function (rSpecificRole $myobj) use ($myobj) {
		*     // from here, $myobj's fallback role is rSpecificRole
		*     // after this function has executed, the fallback is switched
		*     // back to its previous value   
		* });
		*/
		var $fallback = '';

		/**
		* The DCIObject constructor
		*
		* This function must be called by an inheriting class (parent::__construct())
		* in order for the DCI functionality to work properly.  More specifically
		* this function adds all implemented role methods and properties to the index
		* maintained in this class.
		*/
        function __construct() {
        	foreach (class_implements($this) as $role) {
        	    $roleclass = $role . "Actions";
        	    if (class_exists($roleclass))
        		    $this->addRole($role);
        	}
        }
		
		/**
		* While it is possible to add a role after initialization,
		* the type hinting required by some roles causes errors
		*/
		private function addRole($role) {
		    $roleclass = $role . "Actions";
			
			if (interface_exists($role) && class_exists($roleclass)) {
			    $this->roles[$role] = array();
			    $this->roles[$role]['properties'] = array();
			    $this->roles[$role]['methods'] = array();
			    			    
    			foreach (get_class_vars($roleclass) as $property => $value) {
    			    $this->roles[$role]['properties'][$property] = $value;
    			    
    			    if (array_key_exists($property,$this->properties))
    			        $this->properties[$property][] = $role;
    			    else
    			        $this->properties[$property] = array($role);
    			}
			
    			foreach (get_class_methods($roleclass) as $method) {
    			    $this->roles[$role]['methods'][$method] = true;
    			    
    			    if (array_key_exists($method,$this->methods))
    			        $this->methods[$method][] = $role;
    			    else
    			        $this->methods[$method] = array($role);
    			}
    			
    			$this->fallback = $role;
    		}
    		else throw new RoleNotFoundException($role);
		}
		
		function call_explicit($role, $method, $args) {
		    array_unshift($args, $this);
		    $roleclass = $role . "Actions";
		    
		    if (class_exists($roleclass) && method_exists($roleclass,$method)) {
                return call_user_func_array(array($roleclass,$method),$args);
            }
            else {
                throw new MethodNotFoundException("$role::$method",$this->roles[$role]);
            }
		}
		
		function call_default($method, $args) {
		    if (isset($this->methods[$method])) {
		        return $this->call_explicit(end($this->methods[$method]), $method, $args);
		    }
		}
		
		function call($func, $args) {
		    if (isset($this->methods[$func]) && count($this->methods[$func]) > 1) {
		        return $this->call_explicit($this->fallback, $func, $args);
		    }
		    else if (isset($this->methods[$func]) && is_array($this->methods[$func]))
		        return $this->call_explicit($this->methods[$func][0], $func, $args);
		    else 
		        throw new MethodNotFoundException("::$func",array_keys($this->roles));
		}

		// TODO: Try to determine calling role -- 
		// restrict property access to their creating roles
		function __get($var) {
		    if (array_key_exists($var,$this->properties)) {
		        if (isset($this->properties[$var]) && count($this->properties[$var]) > 1) {
		            throw new AmbiguousPropertyException($var,$this->properties[$var]);
		        }
		        else {
		            return $this->roles[$this->properties[$var][0]]['properties'][$var];
		        }
		    }
		    else {
		        throw new PropertyNotFoundException($var, $this->properties);
		    }
		}
		function __set($var,$value) {
		    if (array_key_exists($var,$this->properties)) {
		        if (isset($this->properties[$var]) && count($this->properties[$var]) > 1) {
		            throw new AmbiguousPropertyException($var,$this->properties[$var]);
		        }
		        else {
		            return $this->roles[$this->properties[$var][0]]['properties'][$var] = $value;
		        }
		    }
		    else {
		        throw new PropertyNotFoundException($var, $this->properties);
		    }
		}
        function __call($func, $args) {
            try {
                return $this->call($func,$args);
            }
            catch (AmbiguousMethodException $ame) {
                // throw a PHP notice to alert the programmer to the ambiguity.
                // they can silence the error by turning off error reporting or
                // using the shutup (@) operator
                trigger_error("<b>Ambiguous Method</b> $func in: " . implode(", ",$this->methods[$func]));
                return $this->call_default($func,$args);
            }
        }
		
	}
?>
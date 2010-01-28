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
	* The DCIObject class is meant to be inherited and as a result provides
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
				    $this->AddRole($role);
			}
		}
		
		function AddRole($role) {
		    $roleclass = $role . "Actions";
			
			if (interface_exists($role) && class_exists($roleclass)) {
			    $this->roles[$role] = array();
			    $this->roles[$role]['properties'] = array();
			    $this->roles[$role]['methods'] = array();
			    
			    // store the rolename of the variable
			    // properties are managed by the DCIObject
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
    			        $this->methods[$method] = array($roleclass);
    			}
    			
    			// initialize the role if it contains initialization code
                if (isset($this->roles[$role]['methods']['init'])) {
		            return call_user_func_array(array($roleclass,'init'),array($this));
                }
    		}
    		else throw new RoleNotFoundException($role);
		}
		
		// TODO: Try to determine calling role -- 
		// restrict property access to their creating roles
		function __get($var) {
		    if (array_key_exists($var,$this->properties)) {
		        if (count($this->properties[$var]) > 1) {
		            throw new AmbiguousPropertyException($var);
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
		        if (count($this->properties[$var]) > 1) {
		            throw new AmbiguousPropertyException($var);
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
		    if (isset($this->methods[$func])) {
		        if (count($this->methods[$func]) > 1) {
		            throw new AmbiguousMethodException($func);
		        }
		        else {
		            array_unshift($args, $this);
		            return call_user_func_array(array($this->methods[$func][0],$func),$args);
		        }
		    }
		    else {
		        throw new MethodNotFoundException($func, $this->methods);
		    }
		}
		
	}
?>
<?php
	/**
	* The DCIObject is any object that can perform roles
	*
	* This class is responsible for performing the background work
	* required for having role-playing objects, which includes
	* reflection, static context redirection, and determining if
	* a role or role action can be played/performed
	*/
	require_once dirname(__FILE__) . "/exceptions.php";
	
	class DCIException extends WaxException {}
	
	/**
	* Thrown when a role method is called that doesn't exist.
	*/
	class MethodNotFoundException extends DCIException {
		function __construct($method, $parent) {
			parent::__construct("$method not found","Roles checked: <ul><li>" . implode("</li><li>",$parent->GetRoles()) . "</li></ul>");
		}
	}
	/**
	* Called when a property is gotten/set but has not been
	* defined by any implemented role
	*/
	class PropertyNotFoundException extends DCIException {
		function __construct($property, $parent) {
			parent::__construct("$property not found",$parent->properties);
		}
	}
	/**
	* Thrown when an implementing role tries to define a property
	* that has already been set by another role
	*/
	class PropertyConflictException extends DCIException {
		function __construct($property, $role, $parent) {
			parent::__construct("$property conflicts in $role",$parent->GetRoles());
		}
	}
	/**
	* Thrown when trying to implement a role that has not been defined
	*/
	class RoleNotFoundException extends DCIException {
		function __construct($role) {
			parent::__construct("$role not found",BlockManager::GetLoadedBlocks(true));
		}
	}
	
	/**
	* The base DCIObject class
	*
	* This class provides base functionality for the rest of the framework.
	* The DCIObject class is meant to be inherited and as a result provides
	* the ability to implement role methods, properties, and performs all 
	* necessary redirection to fully implement DCI
	*
	* @author Joe Chrzanowski <joechrz@gmail.com>
	* @version 1.0
	*/
	class DCIObject {
		/**
		* The suffix to look for when calling role methods
		*
		* Changing this value is not recommended.  You would need
		* to rename every single roleActions class to match the new
		* suffix.
		* 
		* @default "Actions"
		*/
		private $_rolePrefix = "r";			// role prefix 
		private $_roleSuffix = "Actions"; 	// depending on the implementation, some people prefer the term Traits
											// you can change the variable here, but you'll also need to change every
											// implementation including the suffix
		
		
		/**
		* A list of all implemented roles
		*/
		protected $roles = array();
		/**
		* A list of the action classes for all implemented roles
		*/
		protected $roleclasses = array();
		
		/**
		* The role properties array
		*
		* Aggregates and stores the properties from all
		* implemented roles
		*/
		var $properties;
		
		var $parentBlock = NULL;
		
		/**
		* The DCI constructor
		*
		* This function must be called by an inheriting class
		* in order for the DCI functionality to work properly
		*/
		function __construct() {		    
	        // implement necessary roles
			$this->roles = $this->reflectRoles();
			
			foreach ($this->roles as $role) {
				$this->AddRole($role);
			}
		}
		
		/**
		* Calls an injected / role method
		* 
		* This magic function handles the redirection of method
		* calls to their respective role method.
		*
		* @param string $func The role method to call
		* @param array $args The arguments to pass to the method
		*/
		function __call($func, $args) {
			if ($class = $this->can($func)) {
				// uses static functions, so push $this onto the front of the arguments to act as $self in the static context
				// this is not to be confused with PHP's built in static self variable.
				array_unshift($args, $this);
				return call_user_func_array(array($class, $func), $args);
			}
			else {
				throw new MethodNotFoundException($func, $this); 
			}
		}
		/**
		* Gets the current value of a property
		*
		* @param string $var The property name to retrieve
		*/
		function __get($var) {
			if (isset($this->properties[$var]))
				return $this->properties[$var];
			else
				throw new PropertyNotFoundException($var, $this);
		}
		/**
		* Sets the value of a property
		*
		* @param string $var The property to set 
		* @param mixed $value The value to set to this property
		*/
		function __set($var,$value) {
			if ($value instanceof ArraySurrogate) {
				$this->$var = $value;
			}
			else if (array_key_exists($var,$this->properties)) {
				$this->properties[$var] = $value;
			}
			else
				throw new PropertyNotFoundException($var, $this);
		}
		
		/**
		* Returns a list of all the object's properties
		*
		* @param bool $values When true, returns the entire properties array.  When false, returns only property names.
		* @return array Either the properties array or the property names (depending on the value of $values)
		*/
		function Properties($values = false) {
			if ($values) return $this->properties;
			else return array_keys($this->properties);
		}
		
		function GetRoles() { return $this->roles; }
		
		/**
		* Implement a role
		*
		* This function takes the name of a role and implements
		* its functionality.  This function may be called after
		* object initialization
		*
		* @param string $role The role to implement
		*/
		function AddRole($role) {
			if (!is_array($this->roleclasses))
				$this->roleclasses = array();
				
				
			$roleclass = $this->roleClassname($role);
			if (!interface_exists($role)) {
				throw new RoleNotFoundException($roleclass);
			}
			else if (!class_exists($roleclass)) {
				// then it's a role with no tied actions
				// just ignore this
				return;
			}
			else {
				// lookup here once instead of n times in __call
				$roleActions = get_class_methods($roleclass);
				$this->roleclasses[$roleclass] = array();
				
				if (is_array($roleActions)) {
					foreach ($roleActions as $method) {
						$this->roleclasses[$roleclass][$method] = true;
					}
				}
				
				// and add role properties to the DCI object
				$vars = get_class_vars($roleclass);
				foreach ($vars as $var => $value) {	
					if (isset($this->properties[$var]))
						error_log("ERROR: Property conflict: $var in " . get_class($this));
					else if (is_array($value)) {
						$this->$var = new ArraySurrogate($value,false);
					}
					else
						$this->properties[$var] = $value;
				}
				
				if (isset($this->roleclasses[$roleclass]['init'])) {
                    // perform a manual call
                    $roleclass::init($this);
                }

			}
		}
		
		/**
		* Determines which roles were 'implemented' at runtime
		*
		* @access private
		* @return array A list of all roles 'implemented' by the class
		*/
		private function reflectRoles() {
		    /*
			$matches = array();
			$reflection = Reflection::export(new ReflectionClass(get_class($this)),true);
			preg_match_all("/implements ([\w\s,]+)\]/",$reflection,$matches);
			if (isset($matches[1][0])) {
				$allinterfaces = $matches[1][0];
				preg_match_all("(\w+)",$allinterfaces,$matches);			
				$interfaces = $matches;
				return $interfaces[0];
			}
			return array();
			*/
			return class_implements($this);
		}
		
		// checks whether or not it's possible for this Model to perform $action
        // if it can, it returns the static class in which the method is located
        private function can($action = NULL) {
            // checks if this model is capable of performing $role $action
            if (is_array($this->roleclasses)) {
                foreach ($this->roleclasses as $class => $methods) {
                    if (is_array($methods) && isset($methods[$action])) {
                        return $class;
                    }
                }
            }
            return false;
        }
		
		/**
		* Determines a role's actions class.  
		*
		* This function takes the value of $this->_roleSuffix and 
		* appends it to the role name
		*
		* @param string $role The role name 
		* @return string The role suffix appended to the role name
		*/
		private function roleClassname($role) {
			return $role . $this->_roleSuffix;
		}
		
		/**
		* Generate a class that plays a given role or list of roles
		*
		* @static
		* @param mixed $roles The role or array of roles to implement
		* @throws RoleNotFoundException
		* @return DCIObject An object that plays all of the given roles
		*/
		static function Generate($roles) {
			if (!is_array($roles)) $roles = array($roles);
			
			$obj = new DCIObject();
			foreach ($roles as $role) {
		        $obj->AddRole($role);
			}
			return $obj;
		}
	}
?>
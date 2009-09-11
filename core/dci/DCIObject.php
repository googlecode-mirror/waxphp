<?php
	/**
	* The DCIObject is any object that can perform roles
	*
	* This class is responsible for performing the background work
	* required for having role-playing objects, which includes
	* reflection, static context redirection, and determining if
	* a role or role action can be played/performed
	*/
	class DCIObject {
		private $_roleSuffix = "Actions"; 	// depending on the implementation, some people prefer the term Traits
											// you can change the variable here, but you'll also need to change every
											// implementation including the suffix
		
		protected $roles;
		protected $roleclasses;
		
		var $properties;
		
		function __construct() {
			$this->roles = $this->reflectRoles();
			// the default Model constructor looks through the roles to more quickly identify possible classnames
			// it also makes sure that all defined roles actually exist
			foreach ($this->roles as $role) {
				$this->AddRole($role);
			}
		}
		
		// Call an injected method
		function __call($func, $args) {
			if ($class = $this->Can($func)) {
				// uses static functions, so push $this onto the front of the arguments to act as $self in the static context
				// this is not to be confused with PHP's built in static self variable.
				array_unshift($args, $this);
				return call_user_func_array(array($class, $func), $args);
			}
			else {
				return NULL; // instead of throwing an exception, treat it like a message and just return NULL
			}
		}
		
		// Manage a property
		function __get($var) {
			if (isset($this->properties[$var]))
				return $this->properties[$var];
		}
		function __set($var,$value) {
			if (array_key_exists($var,$this->properties)) {
				$this->properties[$var] = $value;
			}
			else
				error_log("access to unknown property $var in " . get_class($this));
		}
		
		function IsClassOrRole($cr) {
			if (isset($this->roleclasses[$cr]) || get_class($this) == $cr) 
				return true;
		}
		
		// function for adding a role -- works after instantiation as well
		// this is the only publicly callable function
		function AddRole($role) {
			if (!is_array($this->roleclasses))
				$this->roleclasses = array();
				
			$roleclass = $this->roleClassname($role);
			if (!interface_exists($role)) {
				throw new UnknownRoleException($roleClass);
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
				
				// and add properties to the DCI object
				$vars = get_class_vars($roleclass);
				foreach ($vars as $var => $value) {
					$this->properties[$var] = $value;
				}
				
				if (isset($this->roleclasses[$roleclass]['init'])) {
					// perform a manual call
					$roleclass::init($this);
				}
			}
		}
		
		// checks whether or not it's possible for this Model to perform $action
		// if it can, it returns the static class in which the method is located
		public function Can($action = NULL) {
			// checks if this model is capable of performing $role $action
			if (is_array($this->roleclasses)) {
				foreach ($this->roleclasses as $class => $methods) {
					if (is_array($methods) && isset($methods[$action])) return $class;
				}
			}
			return false;
		}
		
		public function TryTo($action) {
			if ($this->Can($action) || method_exists($this,$action)) {
				return true;
			}
			else return false;
		}
		
		
		// function to reflect on $this to get which interfaces it implements
		// this is pretty messy... 
		private function reflectRoles() {
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
		}
		
		// converts the role classname to get the traits for it
		private function roleClassname($role) {
			return $role . $this->_roleSuffix;
		}
	}
?>
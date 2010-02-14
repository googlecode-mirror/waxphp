<?php
    abstract class Context {
        function __construct() {
            // reflect the function to determine the casted types
    	    $var = new ReflectionMethod($this,'Execute');
    	    $params = $var->getParameters();

    	    $fallbacks = array();
    	    $types = array();
    	    $args = array();

    	    // for each parameter to the function (the parts before 'use')
    	    foreach ($params as $param) {
    	        $str = $param->__toString();
    	        $matches = array();

    	        // find out which role we're casting too for each var
    	        // Note!: the regex looks for the 'r' prefix before a role name
    	        preg_match_all("/\s+(r[a-z0-9_]+)\s+\\\$([a-z_][a-z0-9_]+)/i",$str,$matches);

    	        // foreach match (realistically 1 or 0 matches will occur)
                foreach ($matches[1] as $index => $role) {
                    $varname = $matches[2][$index];         // the object we're going to be "casting"
                    global $$varname;                       // bring the object into scope
                    $obj = $$varname;                       // create a reference to it
                    $fallbacks[$varname] = $obj->fallback;  // save its current fallback role
                    $obj->fallback = $role;                 // update to the role we're casting to
                    $args[] = $obj;                         // pass the object as an argument to the context
                }
    	    }

    	    // now we can execute the context
    	    $result = call_user_func_array($exfunc,$args);

    	    // undo the casting (put the object back to the way we found it)
    	    foreach ($fallbacks as $varname => $fallback) {
    	        $obj = $$varname;
    	        $obj->fallback = $fallback;
    	    }

    	    // and then return the result of the context call
    	    return $result;
        }
        
        abstract function Execute();
        
        function __toString() {
            return "" . $this->Execute();
        }
    }
?>
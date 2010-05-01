<?php
    class ObjectTargetNotFoundException extends WaxException {
        function __construct($objname, $target) {
            parent::__construct("$objname::$target not found","Could not execute $objname::$target()");
        }
    }
    class InvalidTargetException extends WaxException {
        function __construct($objname) {
            parent::__construct("Invalid Target: $objname","$objname must be instance of WaxObject");
        }
    }
    class ObjectNotFoundException extends WaxException {
        function __construct($objname) {
            parent::__construct("Not Found: $objname","Object: $objname could not be created");
        }
    }

    interface rObjectRouter {
    }
    
    class rObjectRouterActions {
        static function ExecuteTarget(rObjectRouter $self, $objargs = array()) {
            $objname = $self->GetObjectName();
            if (class_exists($objname)) {
                $obj = new $objname();
                if (method_exists($obj, 'initialize'))
                    call_user_func_array(array($obj, "initialize"), $objargs);
                
                if ($obj instanceof WaxObject) {
                    // good
                    $obj->id = $self->GetObjectID();

                    $method = $self->GetMethod();
                    $args = $self->GetArgs();
                    $args[] = $_POST;
                
                    return call_user_func_array(array($obj,$method),$args);
                }
                else {
                    // bad
                    throw new InvalidTargetException($objname);
                }
            }
            else {
                throw new ObjectNotFoundException($objname, "", array());
            }
        }
        static function DetermineViewname(rObjectRouter $self) {
            $viewname = $self->GetObjectName();
            $viewtarget = $self->GetMethod();
            
            $viewname = "$viewname/$viewtarget";
            return $viewname;
        }
    }
    
    
    class Router extends DCIObject implements rObjectRouter {
        var $aliases = array(
                        "objectname"    => "Home",
                        "objectid"      => '',
                        "method"        => "index",
                    );
        var $data = array('args' => array());
        
        function __construct($querystring) {
            parent::__construct();
            
            $parts = explode("/",$querystring);
            
            // take apart the querystring
            $indx = 0;
            $aliases = array_keys($this->aliases);
            foreach ($parts as $part) {
                if ($aliases[$indx] == "objectid" && !is_numeric($part)) {
                    $indx++;
                }
                    
                if (isset($aliases[$indx])) {
                    $this->data[$aliases[$indx++]] = $part;
                }
                else {
                    $this->data['args'][] = $part;
                }
            }
        }
        function __call($func, $args) {
            // if it's a GetVarname() type method, strip out the Get part
            if (preg_match("/^Get/",$func)) {
                $var = strtolower(substr($func, 3));

                if (isset($this->data[$var]) && !empty($this->data[$var])) 
                    return $this->data[$var];
                else if (isset($this->aliases[$var]))
                    return $this->aliases[$var];
            }
            else {
                return parent::__call($func,$args);
            }
        }
    }
?>
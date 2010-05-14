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
                    if (class_exists($obj->GetType() . $self->GetMethod() . "Ctx")) {
                        $ctxname = $obj->GetType() . $self->GetMethod() . "Ctx";
                        $ob_ctx = new $ctxname();
                        
                        $objtype = $obj->GetType();
                        $obj = new $objtype();
                        $obj->id = $self->GetObjectID();
                        
                        $args = $self->GetArgs();
                        if (!$args) 
                            $args = array();
                        array_unshift($args, $obj);
                        
                        if ($ob_ctx instanceof Context)
                            return call_user_func_array(array($ob_ctx, "Execute"), $args);
                        else
                            throw new InvalidContextException($ctxname);
                    }
                    else {
                        $obj->id = $self->GetObjectID();

                        $method = $self->GetMethod();
                        $args = $self->GetArgs();
                        if (!$args)
                            $args = array();
                
                        return call_user_func_array(array($obj,$method),$args);
                    }
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
        
        function __construct($querystring, $default_object = "Home", $default_method = "index") {
            parent::__construct();
            
            $parts = explode("/",$querystring);
            
            $this->aliases['objectname'] = $default_object;
            $this->aliases['method'] = $default_method;
            
            // take apart the querystring
            $indx = 0;
            $aliases = array_keys($this->aliases);
            foreach ($parts as $part) {
                if (isset($aliases[$indx])) {
                    if ($aliases[$indx] == "objectid" && !is_numeric($part)) {
                        $indx++;
                    }
                    
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
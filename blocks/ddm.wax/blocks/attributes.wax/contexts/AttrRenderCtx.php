<?php
    /**
    * This context is responsible for rendering attribute data
    */
    class AttrRenderCtx extends Context {
        function Execute(array $attr, $action, $value = "") {
            $attr['value'] = $value;
            
            $classname = $attr['type'] . "Attribute";
            if (class_exists($classname))
                $attr = new $classname($attr);
            else 
                throw new AttributeTypeNotFoundException($classname, $action);
                
            $xtra_args = array();
            try {
                $xtra_args = $attr->$action();
            }
            catch (RoleMethodNotFoundException $rmnfe) {}
            
            return $attr->Render($action,$xtra_args);
        }
    }
?>
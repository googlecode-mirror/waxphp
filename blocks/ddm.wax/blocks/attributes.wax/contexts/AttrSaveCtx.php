<?php
    /**
    * This context is responsible for rendering attribute data
    */
    class AttrSaveCtx extends Context {
        function Execute(array $attr, array $record = array()) {            
            $classname = $attr['type'] . "Attribute";
            if (isset($record[$attr['name']]))
                $attr['value'] = $record[$attr['name']];
                
            if (class_exists($classname))
                $attr = new $classname($attr);
            else 
                throw new AttributeTypeNotFoundException($classname, $action);
                
            $xtra_args = NULL;
            try {
                $xtra_args = $attr->save($record);
            }
            catch (RoleMethodNotFoundException $rmnfe) {}
            
            return $xtra_args;
        }
    }
?>
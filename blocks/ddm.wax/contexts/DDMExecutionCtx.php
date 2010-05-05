<?php
    class DDMExecutionCtx extends Context {
        function Execute(rObjectRouter $router) {
            parent::Execute();
            
            // The DDMRouteCtx tries to route normally (using the standard ExecuteTarget method)
            // but if it fails, it redirects to the DDM and tries again
            $view_args = array();
            try {
                $view_args = $router->ExecuteTarget();
            }    
            catch (ObjectNotFoundException $onfe) {
                $objtype = $router->GetObjectname();
                $router->data['objectname'] = "DDM";
                $view_args = $router->ExecuteTarget(array($objtype));
            }
            return $view_args;
        }
    }
?>
<?php
    class DDMRenderCtx extends Context {
        function Execute(rObjectRouter $router, WaxBlock $appblock, WaxBlock $ddmblock, array $view_args) {
            $view_name = $router->DetermineViewname();

            $vr_ctx = new ViewRenderCtx();

            /**
            * When extending the DDM block, the views are then looked for in the same block
            * as the child object (IE: Users extends DDM will look for Users/action, even though
            * we want it to load the DDM/action view).
            *
            * This try/catch here redirects the view request to the DDM views if it is not
            * found in the current block.
            */
            $content_for_layout = '';
            
            try {
                // Try finding the view in the application block
                $v = BlockManager::Lookup("views",$view_name);
                $content_for_layout = $vr_ctx->Execute(new View($v), $view_args);
            }
            catch (ResourceNotFoundException $vnfe) {
                try {
                    // Try finding the view in the DDM block
                    // IE: Admin/viewname
                    //     ACL/viewname
                    $view_name = $router->DetermineViewname();
                    $content_for_layout = $vr_ctx->Execute(new View($view_name, $ddmblock), $view_args);
                }
                catch (ViewNotFoundException $vnfe) {
                    // Try finding the view in the DDM block under the DDM object
                    // Dynamic Models eventually end up here if there's no overrides
                    $router->data['objectname'] = 'DDM';
                    $view_name = $router->DetermineViewname();
                    $content_for_layout = $vr_ctx->Execute(new View($view_name, $ddmblock), $view_args);
                }
            }
            
            return $content_for_layout;
        }
    }
?>
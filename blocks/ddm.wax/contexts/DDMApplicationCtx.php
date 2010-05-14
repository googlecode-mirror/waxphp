<?php
    class DDMApplicationCtx extends Context {
        function Execute(rObjectRouter $router, WaxBlock $app, WaxBlock $ddm) {
            $view_args = array();
            try {
                // execute normally
                $ddmctx = new DDMExecutionCtx();
                $view_args = $ddmctx->Execute($router);
            }
            catch (WaxException $we) {
                // send the exception to the Error object --> Overrides any other view
                $router->data['objectname'] = "Error";
                $router->data['method']     = "display";
                $view_args = array("exception" => $we);
            }

            $vrctx = new DDMRenderCtx();
            $content_for_layout = $vrctx->Execute($router, $app, $ddm, $view_args);

            $lr_ctx = new LayoutRenderCtx();
            return $lr_ctx->Execute(new View("layout"), $content_for_layout);
        }
    }
?>
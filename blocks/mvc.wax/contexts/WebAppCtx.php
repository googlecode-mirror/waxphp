<?php
    /**
    * The WebAppCtx is responsible for performing the actions
    * necessary to execute a web application.  These actions include
    * analyzing the querystring to determine the target context,
    * calling the target context, grabbing the resulting data, 
    * rendering the view, and rendering the layout.
    *
    * @author Joe Chrzanowski
    * @version 0.10
    */
    class WebAppCtx extends Context {
        function Execute($layout_override = NULL, $target_override = NULL, $action_override = NULL) {
            $block = BlockManager::LoadBlockAt(getcwd());
            
            $router = new QueryString();
            $route = $router->Analyze($_SERVER['QUERY_STRING']);
            
            $context_name = 'Default';
            if (!is_null($target_override))
                $context_name = $target_override;
            else if (isset($route['context']) && !empty($route['context'])) {
                $context_name = $route['context'];
                unset($route['context']);
            }
            
            $action = 'index';
            if (!is_null($action_override))
                $action = $action_override;
            else if (isset($route['action']) && !empty($route['action'])) {
                $action = $route['action'];
                unset($route['action']);
            }
            
            $context = $context_name . "Ctx";
            if (class_exists($context))
                $ctrl = new $context();
            else
                throw new TargetContextNotFoundException($context);
                
            
            $data_for_view = $ctrl->Execute($action, $route);
            
            $view_ctx = new ViewRenderCtx();
            $content_for_layout = $view_ctx->Execute(new View($block,"$context_name/$action"), $data_for_view);

            $layoutctx = new LayoutRenderCtx();
            $layout = "layout";
            if (!is_null($layout_override))
                $layout = $layout_override;
            return $layoutctx->Execute(new View($block,"layout"), $content_for_layout);
        }
    }
?>
<?php
    class WebAppCtx extends Context {
        function Execute($layout_override = NULL, $ctrl_override = NULL, $action_override = NULL) {
            $block = BlockManager::LoadBlockAt(getcwd());
            $router = new QueryStringRouter();
            
            $controller = $router->controller . "Ctx";
            if (!is_null($ctrl_override))
                $controller = $ctrl_override . "Ctx";
                
            $ctrl = NULL;
            if (class_exists($controller))
                $ctrl = new $controller();
            else if (class_exists("DefaultCtx"))
                $ctrl = new DefaultCtx();
            else
                throw new ControllerNotFoundException($controller);
                
            $action = $router->action;
            if (!is_null($action_override))
                $action = $action_override;
            $data_for_view = $ctrl->Execute($action);
            
            $view = new ViewRenderCtx();
            $content_for_layout = $view->Execute(new View($block,$router->GetTargetView()), $data_for_view);

            $layoutctx = new LayoutRenderCtx();
            $layout = "layout";
            if (!is_null($layout_override))
                $layout = $layout_override;
            return $layoutctx->Execute(new View($block,"layout"), $content_for_layout);
        }
    }
?>
<?php
    class IWHInitCtx extends WebAppCtx {
        function Execute($layout_override = NULL, $target_override = NULL, $action_override = NULL) {
            $router = new QueryString();
            $route = $router->Analyze($_SERVER['QUERY_STRING']);
            
            $iwh = BlockManager::GetBlock("iwh");
            $block = BlockManager::LoadBlockAt(getcwd());
            
            try {
                // try executing the context in the application space
                $result = parent::Execute($layout_override, $target_override, $action_override);
                return $result;
            }
            catch (TargetContextNotFoundException $tcnfe) {
                // otherwise, route to the default iwh dynamic handlers
                $view_override = NULL;
                
                if (!isset($route['action']) || empty($route['action'])) {
                    $route['action'] = 'index';
                }
                
                // check for a view override
                $view = $route['context'] . '/' . $route['action'];
                if (isset($block->views[$view])) {
                    $view_override = $view;
                }
                
                // route to the dynamic model context inside iwh.wax
                return parent::Execute($layout_override, "DDM", $action_override, $view_override, $iwh);
            }
        }
    }
?>
<?php
    class IWHInitCtx extends WebAppCtx {
        function Execute($layout_override = NULL, $target_override = NULL, $action_override = NULL) {
            $router = new QueryString();
            $route = $router->Analyze($_SERVER['QUERY_STRING']);
            
            $iwh = BlockManager::GetBlock("iwh");
            $block = BlockManager::LoadBlockAt(getcwd());
            
            try {
                return parent::Execute($layout_override, $target_override, $action_override);
            }
            catch (TargetContextNotFoundException $tcnfe) {
                $view_override = NULL;
                if (!isset($route['action']) || empty($route['action'])) {
                    $route['action'] = 'index';
                }
                $view = $route['context'] . '/' . $route['action'];
                if (isset($block->views[$view])) {
                    $view_override = $view;
                }
                return parent::Execute($layout_override, "DDM", $action_override, $view_override);
            }
        }
    }
?>
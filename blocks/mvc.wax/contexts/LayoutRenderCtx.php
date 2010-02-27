<?php
    class LayoutRenderCtx extends Context {
        function Execute(rRenderable $layout, $contents) {
            $dhtml = BlockManager::GetDHTMLResources();
            $args = array(
                "content_for_layout" => $contents,
                "css" => $dhtml['css'],
                'js' => $dhtml['js']
            );
            
            return $layout->Render($args);
        }
    }
?>
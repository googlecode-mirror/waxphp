<?php
    /**
    * Takes data from a view and wraps it in a layout
    * along with all the DHTML resources known to the
    * BlockManager.
    *
    * @author Joe Chrzanowski
    * @version 0.10
    */
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
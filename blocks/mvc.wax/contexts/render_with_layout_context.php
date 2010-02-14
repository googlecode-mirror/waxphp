<?php
    class LayoutRenderCtx extends Context {
        var $layout;
        var $contents;
        
        function __construct(rRenderable $layout, $contents) {
            $this->layout = $layout;
            $this->contents = $contents;
        }
        function Execute() {
            $dhtml = BlockManager::GetDHTMLResources();
            $args = array(
                "content_for_layout" => $this->contents,
                "css" => $dhtml['css'],
                'js' => $dhtml['js']
            );
            
            return $this->layout->Render($args);
        }
    }
?>
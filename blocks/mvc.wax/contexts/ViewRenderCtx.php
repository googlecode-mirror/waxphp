<?php
    class ViewRenderCtx extends Context {
        function Execute(rRenderable $view, $args) {
            return $view->Render($args);
        }
    }
?>
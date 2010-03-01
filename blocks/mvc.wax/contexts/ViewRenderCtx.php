<?php
    /**
    * Renders a view via rRenderable::Render
    *
    * @author Joe Chrzanowski
    * @param 0.10
    */
    class ViewRenderCtx extends Context {
        function Execute(rRenderable $view, $args) {
            return $view->Render($args);
        }
    }
?>
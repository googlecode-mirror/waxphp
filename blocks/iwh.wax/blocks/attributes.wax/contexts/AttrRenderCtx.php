<?php
    /**
    * This context is responsible for rendering attribute data
    */
    class AttrRenderCtx extends Context {
        function Execute(array $attr, $action, $value = "") {
            $attr['value'] = $value;
            $attr = new Attribute($attr);
            // look for a context matching this attribute:
            $xtra_args = array();
            $attr_name = $attr->GetType();
            $attrctxname = "${attr_name}AttrCtx";
            if (class_exists($attrctxname)) {
                $attrctx = new $attrctxname();
                $xtra_args = $attrctx->Execute($attr, $action);
            }
            return $attr->Render($action,$xtra_args);
        }
    }
?>
<?php
    interface rRenderableAttribute {
    }
    
    class rRenderableAttributeActions {
        static function Render(rRenderableAttribute $self, $action, array $xtra_args) {
            
            // model getters are dynamic.
            // an attributenotfoundexception is thrown if one of these isn't found
            $type = $self->GetType();
            $args = array(
                "id" => $self->GetID(),
                "name" => $self->GetName(),
                "type" => $self->GetType(),
                "default" => $self->GetDefault(),
                "label" => $self->GetLabel(),
                "value" => $self->GetValue(),
                "options" => $self->GetOptions()
            );
            
            foreach ($xtra_args as $arg => $value) {
                if (!isset($args[$arg]))
                    $args[$arg] = $value;
                else
                    trigger_error("Error-- $arg already set in args array");
            }
            
            $block = BlockManager::GetBlockFromContext(__FILE__);
            $view = new View("$type/$action", $block);
            $vrctx = new ViewRenderCtx();
            
            // render the attributes views
            return $vrctx->Execute($view, $args);
        }
    }
?>
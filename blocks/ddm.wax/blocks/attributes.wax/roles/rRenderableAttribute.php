<?php
    interface rRenderableAttribute {
        function GetID();
        function GetName();
        function GetType();
        function GetDefault();
        function GetLabel();
        function GetValue();
        function GetOptions();
    }
    
    class rRenderableAttributeActions {
        static function Render(rRenderableAttribute $self, $action, array $xtra_args) {
            
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
            $view = new View($block, "$type/$action");
            $vrctx = new ViewRenderCtx();
            
            // render the attributes views
            return $vrctx->Execute($view, $args);
        }
    }
?>
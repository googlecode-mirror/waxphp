<?php
    interface rRenderable {
        function GetViewBlock();
        function GetViewName();
    }
    
    class ViewNotFoundException extends WaxException {
        function __construct($viewfile) {
            parent::__construct("View Not Found","The view '$viewfile' could not be located");
        }
    }
    
    class rRenderableActions {
        static function Render(rRenderable $self, $arguments = array()) {
            $viewfile = "";
            $block = $self->GetViewBlock();
            $viewname = $self->GetViewName();
            try {
                if (!is_null($block))
                    $viewfile = $block->views[$viewname];
                else if (!file_exists($viewname))
                    $viewfile = BlockManager::Lookup("views",$viewname);
                else
                    $viewfile = $viewname;
            }
            catch (ResourceNotFoundException $rnfe) {
                throw new ViewNotFoundException($self->GetViewName(),$block);
            }
            
            if (file_exists($viewfile)) {	
    			if (is_array($arguments)) {
    				extract($arguments);
    			}
			    
    			ob_start();
    			    include($viewfile);
        			$rendered_view = ob_get_contents();
    			ob_end_clean();
    		    
    			return $rendered_view;
    		}
    		else throw new ViewNotFoundException($viewname, $block->views);
        }
    }
?>
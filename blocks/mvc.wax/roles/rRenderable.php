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
                $viewfile = $block->views[$viewname];
            }
            catch (ResourceNotFoundException $rnfe) {
                throw new ViewNotFoundException($self->GetViewName(),$block->views);
            }
             
            if (file_exists($viewfile)) {	
    			if (is_array($arguments)) {
    				foreach ($arguments as $arg => $val) {
    					if (!is_numeric($arg))
    						$$arg = $val;
    					else {
    						$arg = "_$arg";
    						$$arg = $val;
    					}
    				}
    			}
			    
    			ob_start();
    			    require($viewfile);
        			$rendered_view = ob_get_contents();
    			ob_end_clean();
    			
    			return $rendered_view;
    		}
    		else throw new ViewNotFoundException($viewname, $block->views);
        }
    }
?>
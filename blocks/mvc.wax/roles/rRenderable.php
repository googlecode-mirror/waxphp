<?php
    interface rRenderable {}
    
    class ViewNotFoundException extends WaxException {
        function __construct($viewfile) {
            parent::__construct("View Not Found","The view '$viewfile' could not be located");
        }
    }
    
    class rRenderableActions {
        var $block;
        var $viewname;
        
        static function Render(rRenderable $self, $arguments, $return = true) {
            $viewfile = "";
            try {
                $viewfile = $self->block->views[$self->viewname];
            }
            catch (ResourceNotFoundException $rnfe) {
                throw new ViewNotFoundException($self->viewname,$self->block->views);
            }
             
            if (is_string($viewfile) && file_exists($viewfile)) {	
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
    			
    			// the view needs access to its block for resource purposes (images, css, js, mostly)
    			$block = BlockManager::GetBlockFromContext($viewfile);
			    
    			ob_start();
    			    require($viewfile);
        			$rendered_view = ob_get_contents();
    			ob_end_clean();
    			
    			if ($return) return $rendered_view;
    			else echo $rendered_view;
    		}
    		else throw new ViewNotFoundException($self->viewname, $self->block->views);
        }
    }
?>
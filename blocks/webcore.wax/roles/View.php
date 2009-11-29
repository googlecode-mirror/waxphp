<?php
	// the roles for rendering views
	interface rView {}
	
	class ViewNotFoundException extends WaxException {
		function __construct($viewname) {
			parent::__construct("View Not Found","View '$viewname' could not be found.");
		}
	}
	
	class rViewActions {
		static function Path($self, $arguments = array()) {
			// need to find out the app location relative to the document root
			// we can use wax::lookuppath for this
			$rel = Wax::LookupPath("app/");
			
			if (!isset($arguments['controller'])) {
				$stack = debug_backtrace();
				$part = array_shift($stack);
				
				while (!isset($part['object']) && count($stack) > 0) {
					$part = array_shift($stack);
				}
				
				$arguments['controller'] = get_class($part['object']);
			}
			$arguments['controller'] = str_replace("Controller","",$arguments['controller']);
			
			$base = Wax::LookupPath("app/");
			$link = array($arguments['controller'],(isset($arguments['action']) ? $arguments['action'] : 'index'));
			
			foreach ($arguments as $var => $value) {
				if ($var == 'controller' || $var == 'action') continue;
				
				$link[] = "$var:$value";
			}
			return $base . implode("/",$link);
		}
		static function Link(rView $self, $title, $args = array()) {
			 return "<a href='" . $self->Path($args) . "'>$title</a>";
		}
		
		static function Render($self, $viewfile = '', $arguments = NULL) {
			// called in a static context, so we need to use arguments to pass in arguments to the renderer,
			// in most cases, in the form of variable => value
			if (is_null($arguments)) $arguments = array();
			
			$arguments['block'] = BlockManager::GetBlockFromContext($viewfile);
			$arguments['self'] = $self;
			
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
			else {
			    throw new ViewNotFoundException($viewfile);
		    }
		}
		static function RenderShow($self, $viewfile, $arguments = NULL) {
			echo $self->Render($viewfile, $arguments);
		}
	}
?>
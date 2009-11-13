<?php
	// the roles for rendering views
	interface rView extends Role {}
	
	class rViewActions {
		static function PathTo(rView $self, $action = "index", $arguments = array(), $controller = NULL) {
			// need to find out the app location relative to the document root
			// we can use wax::lookuppath for this
			$rel = Wax::LookupPath("app/");
			
			if (is_null($controller)) {
				$stack = debug_backtrace();
				$part = array_shift($stack);
				
				while (!isset($part['object']) && count($stack) > 0) {
					$part = array_shift($stack);
				}
				
				$controller = get_class($part['object']);
			}
			$controller = str_replace("Controller","",$controller);
			
			$base = Wax::LookupPath("app/");
			$link = array($controller,$action);
			foreach ($arguments as $var => $value) {
				$link[] = "$var:$value";
			}
			return $base . implode("/",$link);
		}
		static function LinkTo(rView $self, $title, $action = "index", $arguments = array(), $controller = NULL) {
			 return "<a href='" . $self->PathTo($action,$arguments,$controller) . "'>$title</a>";
		}
		
		static function Render(rView $self, $viewfile = '', $arguments = NULL) {
			// called in a static context, so we need to use arguments to pass in arguments to the renderer,
			// in most cases, in the form of variable => value
			$arguments['block'] = BlockManager::GetBlockFromContext($viewfile);
			
			$arguments['self'] = $self;
			
			if (is_null($arguments)) $arguments = array();
			if (empty($viewfile)) {
				// then try to find the view based on the class name
				
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
		}
		static function RenderShow(rView $self, $viewfile, $arguments = NULL) {
			echo $self->Render($viewfile, $arguments);
		}
	}
?>
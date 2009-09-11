<?php
	// the roles for rendering views
	interface View extends Role {}
	interface ModelViewer extends View {}
	
	// a class for performing rendering outside the standard 
	// object heirarchy
	// for example, when rendering a view from another view
	class Renderer extends DCIObject implements View {}
	
	class ViewActions {
		static function Render(View $self, $basefile, $arguments = NULL) {
			// called in a static context, so we need to use arguments to pass in arguments to the renderer,
			// in most cases, in the form of variable => value
			if (is_null($arguments)) $arguments = array();
			if (file_exists($basefile)) {	
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
				require($basefile);
				$resulting_file = ob_get_contents();
				ob_end_clean();
				
				return $resulting_file;
			}
		}
		static function RenderShow(View $self, $basefile, $arguments = NULL) {
			echo $self->Render($basefile, $arguments);
		}
	}
?>
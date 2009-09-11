<?php
	// have controllers implement view functionality by default
	require_once dirname(__FILE__) . "/View.php";
	interface Controller extends View {}
	
	class ControllerActions {
		var $get;
		var $post;
		var $request;
		var $files;
		var $cookie;
		var $session;
		
		static function init(Controller $self) {
			$self->properties['get'] =& $_GET;
			$self->properties['post'] =& $_POST;
			$self->properties['request'] =& $_REQUEST;
			$self->files = new FilesArr($_FILES);
			$self->cookie = new CookiesArr($_COOKIE);
			$self->session = new SessionArr($_SESSION);
			
			$self->Handle();
		}
		static function Handle(Controller $self) {
			// call the actions to initiate contexts
			// decide where to look for determining routing
			$action = null;
			if (isset($self->request['action']) && !empty($self->request['action']))
				$action = $self->request['action'];
			else $action = 'index';
			
			// route to the proper action
			if ($self->TryTo($action)) {
				$viewvars = $self->$action();
			}
			
			if ($self->TryTo("prerender"))
				$self->preview();
				
			$matches = array();
			$reflection = Reflection::export(new ReflectionClass(get_class($self)),true);
			preg_match_all("/([\w\/_.\s]+)\.php/",$reflection,$matches);
			$leaf_class = $matches[0][0];
			
			$blockname = BlockManager::GetBlocknameFromPath($leaf_class);
			$block = BlockManager::GetBlock($blockname);
			if (file_exists($block->views($action)))
				echo $self->Render($block->views($action), (isset($viewvars) ? $viewvars : null));
			else
				redirect_to(array("action" => 'index'));
		}
	}
?>
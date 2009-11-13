<?php
	// have controllers implement view functionality by default
	require_once dirname(__FILE__) . "/View.php";
	
	// controllers by default are able to render views
	interface rController extends rView {}
	
	class rControllerActions {
		// role properties
		var $get;
		var $post;
		var $request;
		var $files;
		var $cookie;
		var $session;
		
		var $depends;		// list of blocks that need to be loaded for this controller
		var $view = array();// variables to send to the view
		
		var $before_route;	// array or name of functions to run before the action is handled
		var $after_route;	// array or name of functions to handle after the routing occurs
		
		// role methods
		
		static function init(rController $self) {
			$self->properties['get'] =& $_GET;
			$self->properties['post'] =& $_POST;
			$self->properties['request'] =& $_REQUEST;
			$self->files = new FilesArr($_FILES);
			$self->cookie = new CookiesArr($_COOKIE);
			$self->session = new SessionArr($_SESSION);
			
			$self->view = array();
			
			if (isset($self->before_route)) {
				if (!is_array($self->before_route)) $self->before_route = array($self->before_route);
			}
			else $self->before_route = array();
			
			if (isset($self->after_route)) {
				if (!is_array($self->after_route)) $self->after_route = array($self->after_route);
			}
			else $self->after_route = array();
		}
		
		// routes based on the action variable
		static function Handle(rController $self) {
			// call the actions to initiate contexts
			// decide where to look for determining routing
			$action = null;
			if (isset($self->request['action']) && !empty($self->request['action']))
				$action = $self->request['action'];
			else $action = 'index';
			
			// determine the actual controller name:
			$ctrl = get_class($self);
			$ctrl_name = str_replace("Controller","",$ctrl);
			
			// route to the proper action
			if ($self->TryTo($action)) {
				$self->$action();
			}
			
			// run any additional/auxiliary functions
			foreach ($self->before_route as $func) {
				if ($self->TryTo($func))
					$self->$func();
			}
			
			$block = BlockManager::GetBlockFromContext();
			$viewname = $ctrl_name . "/" . $action;
			
			if (file_exists($block->views($viewname)))
				echo $self->Render($block->views($viewname), $self->view);
				
			// run any additional/auxiliary functions
			foreach ($self->after_route as $func) {
				if ($self->TryTo($func))
					$self->$func();
			}
		}
	}
?>
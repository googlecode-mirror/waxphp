<?php
	require_once dirname(__FILE__) . "/View.php";
	
	// controllers by default are able to render views
	interface rController extends rView {}
	
	class ParameterNotFoundException extends WaxException {
	    function __construct($parameter) {
	        parent::__construct("Parameter Not Found: $parameter","Parameter was not found.");
	    }
	}
	
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
		
		// role methods
		static function init(rController $self) {
		    // manually set these properties...
			$self->get = $_GET;
			$self->post = $_POST;
			$self->request = $_REQUEST;
			$self->files = new FilesArr($_FILES);
			$self->cookie = new CookiesArr($_COOKIE);
			$self->session = new SessionArr($_SESSION);
			
			$self->view = new ArraySurrogate();
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
			
			try {
		    	$self->$action();
    		}
    		catch (MethodNotFoundException $mnfe) {
    		    // do nothing... just keep going and try to render the view
    		}
            
			$block = BlockManager::GetBlockFromContext($self);
			$viewname = $ctrl_name . "/" . $action;
			
			echo $self->Render($block->views($viewname), $self->view->ToArray());
		}
		
		static function redirect_to($self, $action = NULL, $controller = NULL, $args = NULL) {
		    // redirect the user somewhere else
		    header("Location: " . url_to($action,$controller,$args));
		    echo "<meta http-equiv='refresh' content='3;url=" . url_to($action,$controller,$args) . "' />";
		    echo "<script type='text/javascript'>location.href='" . url_to($action,$controller,$args) . "';</script>";
		    exit;
		}
	}
?>
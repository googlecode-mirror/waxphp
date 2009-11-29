<?php
	// rAppController depends on rQueryStringRouter
	require_once(dirname(__FILE__) . "/QueryStringRouter.php");
	
	interface rAppController extends rQueryStringRouter  {}
	
	class ControllerNotFoundException extends WaxException {
		function __construct($controller) {
			parent::__construct("Class '" . (empty($controller) ? "<i>Unknown</i>" : $controller) . "Controller' Not Found","You may need to create it or import its parent block.");
		}
	}
	
	// rAppController provides initialization and routing functions
	class rAppControllerActions {
		var $router;
		var $defaultController = "Home";
		
		static function AppInit(rAppController $self, $dirname) {
			// get all of the JS and CSS that various blocks have loaded
			$app = BlockManager::LoadBlockAt($dirname);
			$dhtmlresources = BlockManager::GetDHTMLResources();
			
			$renderer = new View();
			try {
    			echo $renderer->Render($app->views("header"),$dhtmlresources);
    	    }
    	    catch (ViewNotFoundException $vnfe) {
	            echo "View 'header.view.php' could not be found in $dirname/views/header.view.php<br />";
    	    }
			    
			$self->Route();
			
			$ctrler = $self->controller;
			if (empty($ctrler)) {
				$self->controller = $self->defaultController;
			}
				
			if (class_exists($self->controller . "Controller")) {
				$ctrlclass = $self->controller . "Controller";
				$ctrl = new $ctrlclass();
				$ctrl->Handle();
			}
			else {
				throw new ControllerNotFoundException($self->controller);
			}
			
			try {
			    echo $renderer->Render($app->views("footer"));
			}
			catch (ViewNotFoundException $vnfe) {
			    echo "View 'footer.view.php' could not be found in $dirname/views/footer.view.php<br />";
			}
		}
	}

?>
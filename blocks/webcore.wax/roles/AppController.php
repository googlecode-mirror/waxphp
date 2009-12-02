<?php
	// rAppController depends on rQueryStringRouter
	require_once(dirname(__FILE__) . "/QueryStringRouter.php");
	
	interface rAppController extends rQueryStringRouter  {}
	
	class ControllerNotFoundException extends WaxException {
		function __construct($controller) {
			parent::__construct("Class '" . (empty($controller) ? "<i>Unknown</i>" : $controller) . "Controller' Not Found","You may need to create it or import its parent block.");
		}
	}
	class ApplicationNotFoundException extends WaxException {
	    function __construct($dirname) {
	        parent::__construct("Application not Found","'$dirname' does not contain a valid Wax application<br />");
	    }
	}
	
	// rAppController provides initialization and routing functions
	class rAppControllerActions {
		var $router;
		var $defaultController = "Home";
		
		static function AppInit(rAppController $self, $dirname) {
			// get all of the JS and CSS that various blocks have loaded
			if (empty($dirname) || !is_dir($dirname)) {
			    throw new ApplicationNotFoundException($dirname);
			}
			$app = BlockManager::LoadBlockAt($dirname);
			$dhtmlresources = BlockManager::GetDHTMLResources();
			
			$renderer = new View();
			try {
    			echo $renderer->Render($app->views("header"),$dhtmlresources);
    	    }
    	    catch (ViewNotFoundException $vnfe) {
    	        // try loading the one from webcore
    	        try {
    	            $webcore = BlockManager::GetBlock("webcore");
    	            echo $renderer->Render($webcore->views("header"),$dhtmlresources);
    	        }
    	        catch (ViewNotFoundException $vnfe2) {
	                echo "View 'header.view.php' could not be found in $dirname/views/header.view.php<br />";
	            }
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
    			echo $renderer->Render($app->views("footer"),$dhtmlresources);
    	    }
    	    catch (ViewNotFoundException $vnfe) {
    	        // try loading the one from webcore
    	        try {
    	            $webcore = BlockManager::GetBlock("webcore");
    	            echo $renderer->Render($webcore->views("footer"),$dhtmlresources);
    	        }
    	        catch (ViewNotFoundException $vnfe2) {
	                echo "View 'footer.view.php' could not be found in $dirname/views/footer.view.php<br />";
	            }
    	    }
		}
	}

?>
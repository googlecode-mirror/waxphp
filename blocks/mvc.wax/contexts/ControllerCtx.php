<?php
    /**
    * Handles the execution of the controller context and returns the view arguments for use in the 
    * view context
    *
    * Warning: controller actions are not type hinted like regular contexts
    */
    abstract class ControllerCtx extends Context {
        var $view = array();    // the view args
        
        var $get;
        var $post;
        var $request;
        var $session;
        var $files;
        var $cookies;
        
        function __construct() {
            // set up context references to the 6 important superglobals
            $this->get =& $_GET;
            $this->post =& $_POST;
            $this->request =& $_REQUEST;
            
            // these arrays are more specialized
            $this->session = new SessionArr($_SESSION);     // uses session_register and session_unregister
            $this->files = new FilesArr($_FILES);           // read-only
            $this->cookies = new CookiesArr($_COOKIE);      // uses set_cookie
        }
        function Execute($action) {
            if (method_exists($this,$action))
                $this->$action();
            else if (method_exists($this,"index"))
                $this->index();
            else
                throw new ActionNotFoundException(get_class($this), $action);
            return $this->view;
        }
    }
?>
<?php
    /**
    * Handles the execution of the controller context and returns the view arguments for use in the 
    * view context
    */
    abstract class ControllerCtx extends ContextGroup {
        var $view = array();    // the view args
        
        var $get;
        var $post;
        var $request;
        var $session;
        var $files;
        var $cookies;
        
        function __construct($action = NULL) {
            // set up context references to the 6 important superglobals
            $this->get =& $_GET;
            $this->post =& $_POST;
            $this->request =& $_REQUEST;
            
            // these arrays are more specialized
            $this->session = new SessionArr($_SESSION);     // uses session_register and session_unregister
            $this->files = new FilesArr($_FILES);           // read-only
            $this->cookies = new CookiesArr($_COOKIE);      // uses set_cookie
            
            parent::__construct($action);
        }
        function Execute() {
            // controller methods dont return anything-- 
            // just run it and return whatever $this->view is afterwards
            parent::Execute();
            return $this->view;
        }
    }
?>
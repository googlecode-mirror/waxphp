<?php
    /**
    * Handles the execution of the controller context and returns the view arguments for use in the 
    * view context
    *
    * Controller methods receive their arguments via the querystring-- any POST data can 
    * be accessed with $this->post
    *
    * @author Joe Chrzanowski
    * @version 0.10
    */
    abstract class ControllerCtx extends Context {
        protected $view = array();    // the view args
        
        protected $get;
        protected $post;
        protected $request;
        protected $session;
        protected $files;
        protected $cookies;
        
        function __construct() {
            // set up context references to the 6 important superglobals
            $this->get          =& $_GET;
            $this->post         =& $_POST;
            $this->request      =& $_REQUEST;
            
            // these arrays are more specialized
            @session_start();
            $this->session      = new SessionArr($_SESSION);    // uses session_register and session_unregister
            $this->files        = new FilesArr($_FILES);        // read-only
            $this->cookies      = new CookiesArr($_COOKIE);     // uses set_cookie
        }
        
        function Execute($action = "index", array $arguments = array()) {
            if (method_exists($this,$action))
                call_user_func_array(array($this,$action),$arguments);
            else
                throw new ActionNotFoundException(get_class($this), $action);
            return $this->view;
        }
    }
?>
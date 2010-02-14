<?php
    /**
    * DumbModel implements rDataSource
    * which requires that it have accessors for
    *   - type
    *   - data
    * A DumbModel assumes that its data type is the 
    * same as its class name.
    *
    * the DumbModel's data is populated via the rDataRecipient interface.
    */
    class DumbModel extends DCIObject implements rDataSaver {
        var $data;
        
        // set up the default dumb model:
        // no id, empty dataset
        function __construct($data = NULL) {
            parent::__construct();
            
            $this->data = array();
            if (!is_null($data)) {
                $this->data = $data;
            }
        }
        
        // implement the datasource and datarecipient roles
        function GetType() { return get_class($this); }
        function GetData() { return $this->data; }
    }
?>
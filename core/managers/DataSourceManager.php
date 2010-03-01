<?php
    /**
    * The DataSourceManager is responsible for 
    * maintining a cache/list of registered data
    * sources.  Registered data sources are available
    * through the DataSourceManager::Get method.
    *
    * @author Joe Chrzanowski
    * @version 0.10
    */
    class DataSourceManager {
        private static $datasources = array();
        private static $dsconfigs = array();
        private static $last = NULL;
        
        /**
        * Registers a datasource for use later.
        * Datasource names should be unique, such 
        * as a database dsn or an xml file path
        *
        * @param string $name The name of the datasource
        * @param iWaxDataSource $obj The datasource to register
        */
        static function Register($name, iWaxDataSource $obj) {
            self::$datasources[$name] = $obj;
            self::$last = $name;
        }
        
        /**
        * Gets a datasource object.  Returns the most recently 
        * registered datasource if $name is unspecified.
        *
        * @param string $name The name of the data source to retrieve
        * @return iWaxDataSource
        */
        static function Get($name = NULL) {
            if (!is_null($name) && isset(self::$datasources[$name])) {
                return self::$datasources[$name];
            }
            else if (is_null($name) && !is_null(self::$last)) {
                return self::$datasources[self::$last];
            }
            else {
                trigger_error("Error, instance of $name not found in registered datasources");
            }
        }
        
        /**
        * Adds a datasource configuration array to the configuration
        * cache.
        */
        static function Config($name, $array) {
            self::$dsconfigs[$name] = $array;
        }
    }
    
    /**
    * Alias for the DataSourceManager
    */
    class DSM extends DataSourceManager {}
?>
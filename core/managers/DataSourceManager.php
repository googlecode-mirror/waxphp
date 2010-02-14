<?php
    class DataSourceManager {
        private static $datasources = array();
        private static $dsconfigs = array();
        private static $last = NULL;
        
        static function Register($name, $obj) {
            self::$datasources[$name] = $obj;
            self::$last = $name;
        }
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
        
        static function Config($name, $array) {
            self::$dsconfigs[$name] = $array;
        }
    }
    class DSM extends DataSourceManager {}
?>
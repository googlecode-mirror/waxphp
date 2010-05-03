<?php
    /**
    * The iWaxDataSource interface defines the input/output 
    * methods that an object requires to act as a datasource for wax
    *
    * This interface can be extended by a role to create business
    * logic that depends on the iWaxDataSource library
    *
    * @author Joe Chrzanowski
    * @version 0.11
    */
    interface iWaxDataSource {
        function CreateType($name, $attributes = array(), $attr_default = NULL);
        function ExamineType($name, $detailed = false);
        function AlterType($name, $attr_add = NULL, $attr_remove = NULL, $attr_rename = NULL);
        function DeleteType($name);
        function ListTypes();
        
        function AlterAttribute($struct, $attr_data, $option_data = array());
        
        function Find($type, $filters = array());
        function FindByID($type, $id);
        
        function Save($type, $data);
        function Create($type, $data = NULL);
        function Delete($type, $id);
        
        function ACL_Get($recordid = NULL, $resourceid = NULL);
        function ACL_Set($recordid, $resourceid, $allow_deny = "ALLOW");
    }
?>
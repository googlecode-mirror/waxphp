<?php
    /**
    * The iWaxDataSource interface defines the input/output 
    * methods that an object requires to act as a datasource for wax
    *
    * This interface can be extended by a role to create business
    * logic that depends on the iWaxDataSource library
    *
    * @author Joe Chrzanowski
    * @version 0.10
    */
    interface iWaxDataSource {
        function CreateType($name, $attributes);
        function ExamineType($name);
        function AlterType($name, $attr_add = NULL, $attr_remove = NULL, $attr_rename = NULL);
        function DeleteType($name);
        function Find($type, $filters);
        function FindWithID($type, $id);
        function Save($type, $data);
        function Create($type, $data = NULL);
        function Delete($type, $id);
    }
?>
<?php
    interface rPermissionHolder extends rLoginIdentifier {
    }
    
    class rPermissionHolderActions {
        static function IsAllowed(rPermissionHolder $self, $resource) {
            // check if this permission holder has access to this resource
        }
        static function SetPermissions(rPermissionHolder $self, $resource) {
            // set the permissions for a resource in the acl
        }
        static function GetPermissions(rPermissionHolder $self) {
            // look up all ACL entries for this id
        }
    }
?>
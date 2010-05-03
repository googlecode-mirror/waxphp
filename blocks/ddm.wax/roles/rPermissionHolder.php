<?php
    interface rPermissionHolder extends rLoginIdentifier {
    }
    
    class InvalidPermissionsException extends WaxException {
        function __construct($user, $resource) {
            parent::__construct("Unauthorized","You do not have access to $resource with credentials:<pre>" . print_r($user,true) . "</pre>");
        }
    }
    
    class rPermissionHolderActions {
        static function IsAllowed(rPermissionHolder $self, $resource) {
            // check if this permission holder has access to this resource
            $ddm = DSM::Get();
            $permissions = $ddm->ACL_Get($self->id);
            if (isset($permissions[$resource]) && $permissions[$resource] != "DENY")
                return true;
            else {
                foreach ($permissions as $resourceid => $ad) {
                    if ($ad == 'DENY') continue;
                    else if (preg_match("|^$resourceid|",$resource)) return true;
                }
                return false;
            }
        }
        static function SetPermissions(rPermissionHolder $self, $resource) {
            // set the permissions for a resource in the acl
            $ddm = DSM::Get();
            $ddm->ACL_Set($self->id, $resource);
            return true;
        }
        static function GetPermissions(rPermissionHolder $self) {
            // look up all ACL entries for this id
            $ddm = DSM::Get();
            return $ddm->ACL_Get($self->id);
        }
    }
?>
<?php
    interface rACLActionHandler {}
    
    class rACLActionHandlerActions {
        static function index(rACLActionHandler $self) {
        }
        static function owner(rACLActionHandler $self, $record_id) {
            $ddm = DSM::Get();
            $permissions = $ddm->ACL_Get($record_id);
            
            return array("permissions" => $permissions, "record_id" => $record_id);
        }
        static function resource(rACLActionHandler $self, $resource_id) {
            $ddm = DSM::Get();
            $permissions = $ddm->ACL_Get(NULL, $resource_id);
            return array("permissions" => $permissions);
        }
        static function remove_permission(rACLActionHandler $self) {
            $ddm = DSM::Get();
            if ($_POST) {
                $record = $_POST['record_id'];
                $resource = $_POST['resource_id'];
                
                $ddm->ACL_Set($record,$resource,NULL);
                redirect("owner", NULL, array($record));
            }
            else 
                redirect("index");
        }
        static function give_permission(rACLActionHandler $self) {
            $ddm = DSM::Get();
            if ($_POST) {
                $ddm->ACL_Set($_POST['record_id'], $_POST['resource_id']);
                redirect("owner", NULL, array($_POST['record_id']));
            }
            else {
                
            }
        }
    }
?>
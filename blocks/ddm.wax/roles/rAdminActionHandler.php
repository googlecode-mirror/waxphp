<?php
    interface rAdminActionHandler {}
    
    class rAdminActionHandlerActions {
        static function index(rAdminActionHandler $self) {
            $ddm = DSM::Get();
                
            $types = $ddm->ListTypes();
            $typeinfo = array();
            foreach ($types as $id => $type) {
                $typeinfo[$type] = $ddm->ExamineType($type);
            }
            return array('types' => $typeinfo);
        }
        static function create_model(rAdminActionHandler $self) {
            $ddm = DSM::Get();
            
            $_POST['modelname'];
            
            try {
                $ddm->CreateType($_POST['modelname']);
            }
            catch (WaxPDOException $wpdoe) {
                redirect("index");
            }
            redirect("index",$_POST['modelname']);
        }
    }
?>
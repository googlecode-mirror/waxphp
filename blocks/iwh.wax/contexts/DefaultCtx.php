<?php
    class DefaultCtx extends ControllerCtx {
        function index() {
            $ddm = DSM::Get();
            $types = $ddm->ListTypes();
            $details = array();
            foreach ($types as $type) {
                $details[$type] = $ddm->ExamineType($type);
            }
            $this->view['types'] = $details;
        }
        function create_model() {
            $ddm = DSM::Get();
            $modelname = $this->post['modelname'];
            $ddm->CreateType($modelname,array());
            redirect("modify",$modelname);
        }
        function delete_model() {
            $ddm = DSM::Get();
            $ddm->DeleteType($this->get['model']);
            redirect("index");
        }
    }
?>
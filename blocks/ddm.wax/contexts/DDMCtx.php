<?php
    /**
    * Used for "scaffolding" and passing information
    * to the ddm views
    */
    class DDMCtx extends ControllerCtx {
        private function getmodel() {
            return $this->get['context'];
        }
        function index() {
            $ddm = DSM::Get();
            $results = $ddm->Find($this->getmodel());
            $structure = $ddm->ExamineType($this->getmodel(),true);
            $this->view['rows'] = $results;
            $this->view['structure'] = $structure;
        }
        function edit() {
            $ddm = DSM::Get();
            $results = $ddm->FindByID($this->getmodel(), $this->get['id']);
            $record = $ddm->ExamineType($this->getmodel(),true);
            
            $recordlist = array();
            foreach ($ddm->ListTypes() as $type) {
                $recordlist[$type] = $ddm->Find($type);
            }
            $this->view['record_list'] = $recordlist;
            
            foreach ($record as $attr => $details) {
                if (!isset($results[$attr]))
                    $record[$attr]['value'] = '';
                else
                    $record[$attr]['value'] = $results[$attr];
            }
            $record['_id'] = $results['_id'];
            $this->view['record'] = $record;
        }
        function view() {
            $ddm = DSM::Get();
            $results = $ddm->FindByID($this->getmodel(), $this->get['id']);
            $record = $ddm->ExamineType($this->getmodel(),true);
            $this->view['row'] = $results;
            $this->view['types'] = $record;
        }
        function save() {
            $ddm = DSM::Get();
            $ddm->Save($this->getmodel(), $this->post['record']);
            redirect("index");
        }
        function create() {
            $ddm = DSM::Get();
            $this->view['record'] = $ddm->ExamineType($this->getmodel(),true);
            
            $recordlist = array();
            foreach ($ddm->ListTypes() as $type) {
                $recordlist[$type] = $ddm->Find($type);
            }
            $this->view['record_list'] = $recordlist;
        }
        function delete() {
            $ddm = DSM::Get();
            $did = $this->get['delete'];
            $ddm->AlterType($this->getmodel(),NULL,array($did));
            redirect("index");
        }
        
        // functions for altering the data model -- 
        function modify() {
            $ddm = DSM::Get();
            $dmodel = $ddm->ExamineType($this->getmodel(),true);
            foreach ($dmodel as $name => $details) {
                if (is_array($details['options'])) {
                    $dmodel[$name]['options'] = $details['options'];
                }
            }
            $this->view['model'] = $dmodel;
            
            // scan attributes dirs
            $attr_types = array();
            $attr_block = BlockManager::GetBlock("attributes");
            foreach (scandir($attr_block->GetBaseDir() . "/views/") as $attr_type) {
                if ($attr_type[0] == '.' || $attr_type[0] == '_') continue;
                $attr_types[] = $attr_type;
            }
            $this->view['attr_types'] = $attr_types;
        }
        function modify_add() {
            $ddm = DSM::Get();
            if ($this->post) {
                _debug($this->post);
                $attr = array(
                    $this->post['attr']['name'] => $this->post['attr']['type']
                );
                $ddm->AlterType($this->getmodel(), $attr);
            }
            redirect("modify");
        }
        function modify_rename() {
            $ddm = DSM::Get();
            if ($this->post) {
                $alter = array();
                $options = array();
                foreach ($this->post['model'] as $attr => $details) {
                    if (isset($details['options'])) {
                        $options = $details['options'];
                        unset($details['options']);
                    }
                    $ddm->AlterAttribute($attr, $details, $options);
                }
            }
            redirect("modify");
        }
        function modify_remove() {
            $ddm = DSM::Get();
            if (isset($this->get['delete'])) {
                $ddm->AlterType($this->getmodel(), NULL, array($this->get['delete']));
            }
            redirect("modify");
        }
    }
?>
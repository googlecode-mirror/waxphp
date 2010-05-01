<?php
    interface rScaffolder {}
    
    /**
    * self role is responsible for basic scaffolding.  Since the 
    * scaffolder uses the DynamicModel backend, the views don't 
    * necessarily need to be modified unless custom functionality
    * is needed.
    */
    class rScaffolderActions {
        static function index(rScaffolder $self) {
            $ddm = DSM::Get();
            $results = $ddm->Find($self->GetType());
            $structure = $ddm->ExamineType($self->GetType(),true);

            return array(
                'rows' => $results,
                'structure' => $structure
            );
        }
        static function edit(rScaffolder $self) {
            $ddm = DSM::Get();
            $results = $ddm->FindByID($self->GetType(), $self->id);
            $record = $ddm->ExamineType($self->GetType(),true);
            
            $view = array();
            
            $recordlist = array();
            foreach ($ddm->ListTypes() as $type) {
                $recordlist[$type] = $ddm->Find($type);
            }
            $view['record_list'] = $recordlist;
            
            foreach ($record as $attr => $details) {
                if (!isset($results[$attr]))
                    $record[$attr]['value'] = '';
                else
                    $record[$attr]['value'] = $results[$attr];
            }
            $record['_id'] = $results['_id'];
            $view['record'] = $record;
            
            return $view;
        }
        static function view(rScaffolder $self) {
            $ddm = DSM::Get();
            
            $results = $ddm->FindByID($self->GetType(), $self->id);
            $record = $ddm->ExamineType($self->GetType(),true);
            
            return array(
                'row' => $results,
                'types' => $record
            );
        }
        static function save(rScaffolder $self) {
            $ddm = DSM::Get();
            $ddm->Save($self->GetType(), $_POST['record']);
            redirect("index");
        }
        static function create(rScaffolder $self) {
            $ddm = DSM::Get();
            $view = array();
            
            $view['record'] = $ddm->ExamineType($self->GetType(),true);
            
            $recordlist = array();
            foreach ($ddm->ListTypes() as $type) {
                $recordlist[$type] = $ddm->Find($type);
            }
            $view['record_list'] = $recordlist;
            return $view;
        }
        static function delete(rScaffolder $self) {
            $ddm = DSM::Get();
            $did = $_GET['delete'];
            $ddm->AlterType($self->GetType(),NULL,array($did));
            redirect("index");
        }
    }
?>
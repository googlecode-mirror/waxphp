<?php
    interface rPointerAttrActionHandler {
        
    }
    
    class rPointerAttrActionHandlerActions {
        static function view(rPointerAttrActionHandler $self) {
            $ddm = DSM::Get();
            $options = $self->GetOptions();
            
            $record = $ddm->FindByID($options['type'], $self->GetValue());
            
            return array('link_label' => $record[$options['label']]);
        }
        static function edit(rPointerAttrActionHandler $self) {
            $ddm = DSM::Get();
            
            $opts = $self->GetOptions();
            $ret = array();
            
            if (isset($opts['type'])) {
                $records = $ddm->Find($opts['type']);
                $rfv = array();
                foreach ($records as $r) {
                    $rfv[$r['_id']] = $r[$opts['label']];
                }
                $ret['records'] = $rfv;
            }
            else $ret['records'] = array();
            
            return $ret;
        }
        static function editor(rPointerAttrActionHandler $self) {
            $ddm = DSM::Get();
            $types = $ddm->ListTypes();
            $view = array();
            $view['types'] = $types;
            
            $options = $self->GetOptions();
            
            $descs = array();
            if (!isset($options['type'])) {
                foreach ($types as $tid => $type) {
                    $descs[$type] = $ddm->ExamineType($type);
                }
                $vals = array_values($types);
            }
            else
                $descs = array($options['type'] => $ddm->ExamineType($options['type']));
                
            $view['typeattrs'] = $descs;
            return $view;
        }
    }
?>
<?php
    class PointerAttrCtx extends AttrCtx {
        function view() {
            $ddm = DSM::Get();
            $options = $this->attribute->GetOptions();
            $record = $ddm->FindByID($options['type'], $this->attribute->GetValue());
            
            $this->view['link_label'] = $record[$options['label']];
        }
        function edit() {
            $ddm = DSM::Get();
            
            $opts = $this->attribute->GetOptions();
            if (isset($opts['type'])) {
                $records = $ddm->Find($opts['type']);
                $rfv = array();
                foreach ($records as $r) {
                    $rfv[$r['_id']] = $r[$opts['label']];
                }
                $this->view['records'] = $rfv;
            }
            else $this->view['records'] = array();
        }
        function editor() {
            $ddm = DSM::Get();
            $types = $ddm->ListTypes();
            $this->view['types'] = $types;
            
            $options = $this->attribute->GetOptions();
            $descs = array();
            if (!isset($options['type'])) {
                foreach ($types as $tid => $type) {
                    $descs[$type] = $ddm->ExamineType($type);
                }
                $vals = array_values($types);
            }
            else
                $descs = array($options['type'] => $ddm->ExamineType($options['type']));
                
            $this->view['typeattrs'] = $descs;
        }
    }
?>
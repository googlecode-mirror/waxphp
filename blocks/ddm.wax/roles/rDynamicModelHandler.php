<?php
    interface rDynamicModelHandler {
        function GetType();
    }
    
    /**
    * This role is responsible for the methods that modify the dynamic datamodels
    * Including modifying attributes, adding and removing attribtues, and modifying
    * attribute options.
    */
    class rDynamicModelHandlerActions {
        static function modify(rDynamicModelHandler $self) {
            $ddm = DSM::Get(); 
            $view = array();
            
            $dmodel = $ddm->ExamineType($self->GetType(),true);
            foreach ($dmodel as $name => $details) {
                if (is_array($details['options'])) {
                    $dmodel[$name]['options'] = $details['options'];
                }
            }
            $view['model'] = $dmodel;
            
            // scan attributes dirs
            $attr_types = array();
            $attr_block = BlockManager::GetBlock("attributes");
            foreach (scandir($attr_block->GetBaseDir() . "/views/") as $attr_type) {
                if ($attr_type[0] == '.' || $attr_type[0] == '_') continue;
                $attr_types[] = $attr_type;
            }
            $view['attr_types'] = $attr_types;
            
            return $view;
        }
        static function modify_add(rDynamicModelHandler $self) {
            $ddm = DSM::Get();
            if ($_POST) {
                $attr = array(
                    $_POST['attr']['name'] => $_POST['attr']['type']
                );
                $ddm->AlterType($self->GetType(), $attr);
            }
            redirect("modify");
        }
        static function modify_rename(rDynamicModelHandler $self) {
            $ddm = DSM::Get();
            if ($_POST) {
                $alter = array();
                $options = array();
                foreach ($_POST['model'] as $attr => $details) {
                    if (isset($details['options'])) {
                        $options = $details['options'];
                        unset($details['options']);
                    }
                    $ddm->AlterAttribute($attr, $details, $options);
                }
            }
            redirect("modify");
        }
        static function modify_remove(rDynamicModelHandler $self) {
            $ddm = DSM::Get();
            if (isset($this->get['delete'])) {
                $ddm->AlterType($self->GetType(), NULL, array($this->get['delete']));
            }
            redirect("modify");
        }
    }
?>
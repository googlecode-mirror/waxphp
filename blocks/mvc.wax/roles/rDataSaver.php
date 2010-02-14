<?php
    interface rDataSaver {
        function GetType();
        function GetData();
    }
    class rDataSaverActions {
        static function Create(rDataSaver $self) {
            $ds = DSM::Get();
            return $ds->Create($self->GetType(), $self->GetData());
        }
        static function Save(rDataSaver $self) {
            $ds = DSM::Get();
            $type = $self->GetType();
            $data = $self->GetData();
            return $ds->Save($type,$data);
        }
        static function Delete(rDataSaver $self) {
            $ds = DSM::Get();
            $data = $self->GetData();
            if (isset($data['id']))
                $ds->Delete($self->GetType(),$data['id']);
        }
    }
?>
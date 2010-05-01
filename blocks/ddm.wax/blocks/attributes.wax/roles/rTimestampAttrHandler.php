<?php
    interface rTimestampAttrActionHandler {}
    
    class rTimestampAttrHandlerActions {
        static function editor(rTimestampAttrActionHandler $self) {
            $formats = array(
                "F d, Y g:i A",
                "m.d.y",
                "Ymd",
                "D M j G:i:s T Y",
                "Y-m-d"
            );
            return array('formats' => $formats) ;
        }
    }
?>
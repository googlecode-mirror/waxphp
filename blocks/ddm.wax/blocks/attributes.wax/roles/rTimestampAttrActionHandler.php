<?php
    interface rTimestampAttrActionHandler {}
    
    class rTimestampAttrActionHandlerActions {
        static function editor(rTimestampAttrActionHandler $self) {
            $formats = array(
                "F d, Y g:i A",
                "F d, y",
                "m.d.y",
                "Ymd",
                "D M j G:i:s T Y",
                "Y-m-d"
            );
            return array('formats' => $formats) ;
        }
    }
?>
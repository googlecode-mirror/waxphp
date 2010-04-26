<?php
    class TimestampAttrCtx extends AttrCtx {
        function editor() {
            $formats = array(
                "F d, Y g:i A",
                "m.d.y",
                "Ymd",
                "D M j G:i:s T Y",
                "Y-m-d"
            );
            $this->view['formats'] = $formats;
        }
    }
?>
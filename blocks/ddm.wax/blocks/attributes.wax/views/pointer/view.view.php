<?php
    if (!empty($value))
        echo link_to($link_label,"view",$options['type'],array("id" => $value));
    else
        echo "NULL";
?>
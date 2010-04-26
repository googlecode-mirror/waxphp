<?=
    (!empty($value) ? 
        date(
        (
            isset($options['custom_format']) ? 
            $options['custom_format'] : 
            $options['format']
        )
        ,$value
        )
    : "<i>No Timestamp</i>"
    )
?>
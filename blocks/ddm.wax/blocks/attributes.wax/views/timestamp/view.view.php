<?
    echo (!empty($value) ? 
        date(
        (
            $options['format'] == 'custom' ? 
            $options['custom_format'] : 
            $options['format']
        )
        ,$value
        )
    : "<i>No Timestamp</i>"
    )
?>
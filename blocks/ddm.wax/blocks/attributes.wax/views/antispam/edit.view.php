<input type='hidden' id='record_<?=$id?>_<?=$name?>' name='record[<?=$name?>]' value="<?=$num?>" />
<input type='hidden' id='record_<?=$id?>_<?=$name?>2' name='record[<?=$name?>2]' value="<?=$num2?>" />
<input type='hidden' id='record_<?=$id?>_<?=$name?>_confirm' name='record[<?=$name?>_confirm]' value='' />

<div id='antispam_labelfor_<?=$id?>' style='width:150px; border-color:red; background:#FFAAAA; border-width:2px; border-style:solid; padding:8px;'>Verification Failed</div>

<script type='text/javascript'>
    /**
    * Perform a simple javascript calculation 
    */
    Event.observe(window, 'load', function() {
        antispam_compute(<?=$id?>,'<?=$name?>');
    });
</script>
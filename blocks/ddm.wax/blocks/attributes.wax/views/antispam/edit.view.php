<input type='hidden' id='record_<?=$name?>' name='record[<?=$name?>]' value="<?=$num?>" />
<input type='hidden' id='record_<?=$name?>2' name='record[<?=$name?>2]' value="<?=$num2?>" />
<input type='hidden' id='record_<?=$name?>_confirm' name='record[<?=$name?>_confirm]' value='' />

<script type='text/javascript'>
    /**
    * Perform a simple javascript calculation 
    */
    Event.observe(window, 'load', function() {
        var num1 = $("record_<?=$name?>").value * 1;
        var num2 = $("record_<?=$name?>2").value * 1;
        var result = num1 + num2;
        
        var confirmfield = $("record_<?=$name?>_confirm").value = result;
    });
</script>
<select name='record[<?=$name?>]'>
<?php foreach ($options as $indx => $label): ?>
    <option value='<?=$indx?>'><?=$label?></option>
<?php endforeach; ?>
</select>
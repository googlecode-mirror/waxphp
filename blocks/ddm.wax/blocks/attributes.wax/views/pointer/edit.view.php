<select name='record[<?=$name?>]'>
    <?php foreach ($records as $rid => $label): ?>
        <option value='<?=$rid?>'><?=$label?></option>
    <?php endforeach; ?>
</select>
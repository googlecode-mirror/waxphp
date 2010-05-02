<br />
Hash Function: 
<select name='model[<?=$id?>][options][hashfunc]'>
    <?php foreach ($hashfuncs as $hf): ?>
        <option value='<?=$hf?>'<?=(isset($options['hashfunc']) && $options['hashfunc'] == $hf ? ' selected' : '')?>>
            <?=$hf?>
        </option>
    <?php endforeach; ?>
</select><br />
Salt: <input type='text' name='model[<?=$id?>][options][salt]' value='<?=(isset($options['salt']) ? $options['salt'] : '')?>' />
Type: 
<select onChange="attr_pointer_update_typeattrs(<?=$id?>)" name='model[<?=$id?>][options][type]'>
    <?php foreach ($types as $tid => $type): ?>
        <option value='<?=$type?>' <?=($type == $options['type'] ? "selected" : ""); ?>><?=$type?></option>
    <?php endforeach; ?>
</select>
<br />
Label:
<select name='model[<?=$id?>][options][label]'>
    <?php foreach ($typeattrs[$options['type']] as $attr => $type): ?>
        <option value="<?=$attr?>"><?=$attr?></option>
    <?php endforeach; ?>
</select>
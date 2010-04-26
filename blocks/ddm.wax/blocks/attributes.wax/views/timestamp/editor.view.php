Display Format:
<select id='model_<?=$id?>_options_format' name='model[<?=$id?>][options][format]'>
    <?php foreach ($formats as $format): ?>
        <option value='<?=$format?>' <?=($option['format'] == $format ? "selected='selected'" : "")?>><?=date($format)?></option>
    <?php endforeach; ?>
    <option value='custom'>Custom</option>
</select>
<br />
Custom Format:
<input onKeyUp='attr_timestamp_set_custom(<?=$id?>)' name='model[<?=$id?>][options][custom_format]' value="<?=(isset($options['custom_format']) ? $options['custom_format'] : '')?>" />

<a href='http://www.php.net/date' target="_blank">more info</a>
<input type='hidden' name='record[<?=$name?>]' value='<?=time()?>' />
<?=date((!empty($options['custom_format']) ? $options['custom_format'] : $options['format']),time())?>
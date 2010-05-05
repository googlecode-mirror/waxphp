<input type='hidden' name='record[<?=$name?>]' value='<?=time()?>' />
<?=date((($options['format'] == 'custom') ? $options['custom_format'] : $options['format']),time())?>
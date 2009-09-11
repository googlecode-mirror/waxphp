<select <?=$attributes?>>
	<?php foreach ($options as $name => $value): ?>
	<option value='<?=$name?>'<?=($name == $default_value ? ' selected' : "")?>><?=$value?></option>
	<?php endforeach; ?>
</select>
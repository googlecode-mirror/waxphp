<?php foreach ($options as $opt): ?>
<input type='text' name='model[<?=$id?>][options][]' value='<?=$opt?>' /><br />
<?php endforeach; ?>
<input type='text' name='model[<?=$id?>][options][]' />
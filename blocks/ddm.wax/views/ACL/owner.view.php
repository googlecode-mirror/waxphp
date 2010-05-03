<h3>Current Permissions for <?=$record_id?></h3>

<?php foreach ($permissions as $resource => $permission): ?>
<form method="POST" action="<?=url_to('remove_permission')?>">
    <input type='submit' value='Remove Permissions' />
    <?=$resource?>
    <input type='hidden' name='record_id' value='<?=$record_id?>' />
    <input type='hidden' name='resource_id' value='<?=$resource?>' />
</form>
<?php endforeach; ?>
<hr />
<form method="POST" action="<?=url_to("give_permission");?>">
    <h3>Give Permission</h3>
    
    Resource:
    <input type='text' name='resource_id' />
    <input type='hidden' name='record_id' value='<?=$record_id?>' />
    
    <br />
    <input type='submit' value='Save Permissions' />
</form>
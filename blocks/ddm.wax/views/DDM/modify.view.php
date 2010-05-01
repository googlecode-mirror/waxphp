<h3>Actions</h3>
<?=link_to("List Records","index")?><br />
<?=link_to("Insert New Record","create")?>
<h3>Rename Attributes</h3>
<br />
<form method='post' action='<?=url_to('modify_rename')?>'>
<div id='attr_holder'>
    <?php foreach ($model as $attr => $details): ?>
    <ul class='attr_list
    ' id='attr_<?=$details['id']?>'>
        <?php
            echo render_view(
                $block,
                "DDM/attr_edit",
                array(
                    "attr" => $details,
                    "types" => $attr_types
                )
            );
        ?>
    </ul>
    <?php endforeach; ?>
</div>
<input type='submit' value='Save Model' />
</form>
<br />
<h3>Add An Attribute</h3>
<form method='post' action='<?=url_to('modify_add')?>'>
    <input type='text' name='attr[name]' />
    <select name='attr[type]'>
    <?php foreach ($attr_types as $type): ?>
        <option value="<?=$type?>"><?=$type?></option>
    <?php endforeach; ?>
    </select>
    <input type='submit' value='Add Attribute' />
</form>
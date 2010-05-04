<h3>Create a New Data Model</h3>

<form method='post' action='<?=url_to('create_model')?>'>
    <input type='text' name='modelname' />
    <input type='submit' value='Create Model' />
</form>

<h3>Data Models:</h3>

<?php foreach ($types as $type => $attrs): ?>
    <table style='width:500px;'>
        <tr>
            <th colspan='2'><?=$type?></th>
        </tr>
        <?php foreach ($attrs as $attr => $attr_type): ?>
            <tr>
                <td style='width:50%;'><?=$attr?></td>
                <td><?=$attr_type?></td>
            </tr>
        <?php endforeach; ?>
        <tfoot>
            <tr>
                <td colspan='2'>
                    <?=link_to("Create New $type","create",$type);?> | 
                    <?=link_to("Edit this Model","modify",$type);?> | 
                    <?=link_to("Delete this Model","delete_model",NULL,array("model" => $type));?> | 
                    <?=link_to("Manage Model","index",$type);?>
                </td>
            </tr>
        </tfoot>
    </table><br />
<?php endforeach; ?>
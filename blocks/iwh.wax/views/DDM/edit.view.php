<form method='POST' action='<?=url_to("save")?>'>
<input type='hidden' name='record[_id]' value='<?=$record["_id"]?>' />
<table style='width:75%;'>
    <thead>
        <tr>
            <th>Attr.</th>
            <th>Value</th>
        </tr>
    </thead>
    <?php foreach ($record as $attr => $details): ?>
        <?php if ($attr == '_id') continue; ?>
        <tr>
            <td><?=$attr?></td>
            <td>
                <?php
                $attrctx = new AttrRenderCtx();
                echo $attrctx->Execute($details, "edit", $details['value']);
                ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </table>
    <input type='submit' value='Save' />
</form>
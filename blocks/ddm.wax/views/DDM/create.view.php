<form method='POST' action='<?=url_to("save")?>'>
    <table>
        <tr>
            <th>Attribute</th>
            <th>Value</th>
        </tr>
        
    <?php foreach ($record as $attr => $details): ?>
        <tr>
        <?php if ($attr == '_id') continue; ?>
        <td><?=$attr?></td>
            <td>
            <?php
                $attrctx = new AttrRenderCtx();
                echo $attrctx->Execute($details, "edit");
            ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </table>
    <input type='submit' value='Save' />
</form>
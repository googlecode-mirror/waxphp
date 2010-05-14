<?php if (count($rows) > 0): ?>
<table style='width:100%;'>
    <thead>
        <tr>
        <?php 
            foreach (array_keys($structure) as $col): ?>
            <th><?=$col?></th>
        <?php endforeach; ?>
        <th>&nbsp;</th>
        </tr>
    </thead>
    <?php foreach ($rows as $row): ?>
        <tr>
            <?php foreach ($structure as $col => $value): ?>
                <td><?
                    if ($col == "_id") {
                        echo $value;
                        continue;
                    }

                    $attrctx = new AttrRenderCtx();
                    echo $attrctx->Execute($value,"view",(isset($row[$col]) ? $row[$col] : ""));            
                ?></td>
            <?php endforeach; ?>
            <td style='white-space:nowrap;'>
                <?=link_to("edit record","edit", (isset($type) ? $type : NULL), array("_id" => $row['_id']))?> | 
                <?=link_to("delete record","delete", (isset($type) ? $type : NULL), array("_id" => $row['_id']))?>
            </td>
        </tr>
    <?php endforeach; ?>
    
</table>
<?php else: ?>
    <b>This model has no records</b>
<?php endif; ?>
<br />
<?php if (!isset($showlinks) || $showlinks): ?>
<?=link_to("Create New Record","create");?> | 
<?=link_to("Alter Data Model","modify");?> | 
<?=link_to("Main Page","index","Home");?>
<?php endif; ?>
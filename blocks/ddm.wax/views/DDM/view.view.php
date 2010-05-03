<table>
    <?php foreach ($row as $col => $value): ?>
        <tr>
            <td><?=$col?></td>
            <td><?
                if ($col == "_id") {
                    echo $value;
                    continue;
                }
            
                $attrctx = new AttrRenderCtx(); 
                echo $attrctx->Execute($types[$col],"view",$value);
            ?></td>
        </tr>
    <?php endforeach; ?>
</table>
<br />
<br />
<?=link_to("edit record","edit", (isset($type) ? $type : NULL), array("id" => $row['_id']))?> | 
<?=link_to("delete record","delete",(isset($type) ? $type : NULL), array("id" => $row['_id']))?>

<?php
    /**
    * Renders a list item that contains the controls necessary
    * to edit a model attribute
    *
    * needs: 
    *   existing attribute info ($attr)
    */
?>
<li id='attr_<?=$attr['id']?>'>
    <?=link_to('delete attribute','modify_remove',NULL,array('delete' => $attr['name']))?>
    <input type='text' name='model[<?=$attr['id']?>][name]' value='<?=$attr['name'];?>' />
    <select name='model[<?=$attr['id']?>][type]'>
        <?php foreach ($types as $type): ?>
            <?php if ($type[0] == '.') continue; ?>
            
            <option value="<?=$type?>" <?=($type == $attr['type'] ? "selected" : "")?>><?=$type?></option>
        <?php endforeach; ?>
    </select>
    <input type='text' name='model[<?=$attr['id']?>][label]' size='50' value='<?=$attr['label']?>'><br />
    <?php
        $attrctx = new AttrRenderCtx();
        echo $attrctx->Execute($attr,"editor");
    ?>
</li>

<?php ?>
<label class="control-label" ><?= $label ?></label><br>
<select 
    <?php if(!is_null($id)): ?>id="<?= $id ?>"<?php endif ?> 
    class="form-control" size="<?= $size ?>" 
    style="width: 100%" name="<?= $field ?>" 
    <?php if($isMultiple): ?>multiple<?php endif ?> 
>
    <?php foreach($array as $item): ?>
    <option
        <?php if(in_array($item->id, $checkedArray)): ?>
            selected
            <?php if(!is_null($itemDataField) && !is_null($itemDataValue)): ?>
                data-<?= $itemDataField ?>="<?= implode(',',$itemDataValue) ?>"
            <?php endif ?>
        <?php endif ?>
        value="<?= $item->id ?>"
    ><?= $item->$displayField ?></option>
    <?php endforeach ?>
</select>

<?php
use yii\helpers\Html;
?>
<div class="news-categories--item">
    <?= Html::a($model->name, ['', 'developer' => $model->newbuildingComplexes[0]->developer_id]) ?>
    
    <ul class="sub-categories">
    <?php foreach($model->newbuildingComplexes as $newbuildingComplex): ?>
        <?php if($newbuildingComplex->active): ?>
            <li <?php if($newbuildingComplex->id == $newbuildingComplexId): ?>
                class="active"
                <?php endif; ?>>
                <?= Html::a($newbuildingComplex->name, ['', 'newbuilding-complex' => $newbuildingComplex->id]) ?>
            </li>
        <?php endif; ?>
    <?php endforeach; ?>
    </ul>
</div>
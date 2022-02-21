<?php
use yii\helpers\Html;
?>

<?= Html::a($model->name, ['', 'developer' => $model->newbuildingComplexes[0]->developer_id]) ?>

<ul class="dashed">
    <?php foreach($model->newbuildingComplexes as $newbuildingComplex): ?>
        <li><?= Html::a($newbuildingComplex->name, ['', 'newbuilding-complex' => $newbuildingComplex->id]) ?></li>
    <?php endforeach; ?>
</ul>


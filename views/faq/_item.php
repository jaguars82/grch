<?php

use yii\helpers\Html;
use \yii\helpers\StringHelper;
?>
<div class="faq-item" style="margin-bottom: 20px;">
    <h3><?= Html::a($model->name, ['view',  'id' => $model->id]) ?></h3>
    <p>
        <?= StringHelper::truncate(strip_tags($model->text), 100, '...'); ?>
    </p>
</div>
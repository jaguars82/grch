<?php
/* @var $model app\models\Bank */

use yii\helpers\Html;
use yii\helpers\Url;
?>

<div class="col-md-4 text-center">
    <div class="panel panel-default">
        <div class="panel-body" style="height: 150px;">
            <a href="<?= Url::to(['bank/view', 'id' => $model->id]) ?>" style="line-height: 130px">
                <?php if(!is_null($model->logo)): ?>
                    <?= Html::img([Yii::getAlias("@web/uploads/{$model->logo}")], ['style' => 'max-height: 100%']) ?>
                <?php else: ?>
                    <?= Html::img([Yii::getAlias("@web/img/bank.png")], ['height' => '100%']) ?>
                <?php endif ?>
            </a>
        </div>
        
        <div class="panel-footer">
            <?= Html::a($model->name, ['bank/view', 'id' => $model->id]) ?>
        </div>
    </div>
</div>
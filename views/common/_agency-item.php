<?php
use yii\helpers\Html;
use yii\helpers\Url;

$format = \Yii::$app->formatter;
?>
<div class="<?= $colClass ?>">
    <div class="agency-item">
        <div class="image">
            <a href="<?= Url::to(['agency/view', 'id' => $model->id]) ?>">
                <?php if(!is_null($model->logo)): ?>
                    <?= Html::img([Yii::getAlias("@web/uploads/{$model->logo}")], ['style' => 'max-height: 100%']) ?>
                <?php else: ?>
                    <?= Html::img([Yii::getAlias("@web/img/office.png")], ['height' => '100%']) ?>
                <?php endif ?>
            </a>
        </div>
        <p class="title">
            <?= Html::a($model->name, ['agency/view', 'id' => $model->id]) ?>
        </p>
        <?= $this->render('/common/_list-contacts', [
            'model' => $model,
            'class' => 'border-top'
        ]); ?>
    </div>
</div>
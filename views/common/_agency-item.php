<?php
use yii\helpers\Html;
use yii\helpers\Url;

$format = \Yii::$app->formatter;
?>
<div class="<?= $colClass ?>">
    <a href="<?= Url::to(['agency/view', 'id' => $model->id]) ?>">
        <div class="agency-item hover-accent">
            <div class="image">
                <?php if(!is_null($model->logo)): ?>
                    <?= Html::img([Yii::getAlias("@web/uploads/{$model->logo}")], ['style' => 'max-height: 100%']) ?>
                <?php else: ?>
                    <?= Html::img([Yii::getAlias("@web/img/office.png")], ['height' => '100%']) ?>
                <?php endif ?>
            </div>
            <p class="title">
                <?= Html::a($model->name, ['agency/view', 'id' => $model->id]) ?>
            </p>
            <?= $this->render('/common/_list-contacts', [
                'model' => $model,
                'class' => 'border-top'
            ]); ?>
        </div>
    </a>
</div>
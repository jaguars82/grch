<?php
use yii\helpers\Html;
use yii\helpers\Url;

$format = \Yii::$app->formatter;
?>
<div class="<?= $colClass ?>">
    <div class="bank-card">
        <div class="image">
            <a href="<?= Url::to( ['bank/view', 'id' => $model->id]) ?>">
                <?php if(!is_null($model->logo)): ?>
                    <?= Html::img([Yii::getAlias("@web/uploads/{$model->logo}")]) ?>
                <?php else: ?>
                    <?= Html::img([Yii::getAlias("@web/img/bank.png")]) ?>
                <?php endif ?>
            </a>
        </div>
        <p class="title">
            <?= Html::a($model->name, ['bank/view', 'id' => $model->id]) ?>
        </p>
        <?= $this->render('/common/_list-contacts', [
            'model' => $model,
            'class' => 'border-top border-bottom'
        ]); ?>
        <?= Html::a('Ипотечные программы', ['bank/view', 'id' => $model->id], ['class' => 'btn btn-white'])?>
    </div>
</div>
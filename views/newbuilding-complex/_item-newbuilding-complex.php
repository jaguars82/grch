<?php
use yii\helpers\Html;
use yii\helpers\Url;

$format = \Yii::$app->formatter;
?>

<div class="col-xs-6 col-sm-4 col-lg-3">
    <div class="nc-list-main--item hover-accent">
        <p class="title">
            <?= Html::a($model->name, ['newbuilding-complex/view', 'id' => $model->id]) ?>
        </p>
        <div class="image">
            <a href="<?= Url::to(['newbuilding-complex/view', 'id' => $model->id]) ?>">
                <?php if(!is_null($model->logo)): ?>
                    <?= Html::img(Yii::getAlias("@web/uploads/{$model->logo}")) ?>
                <?php else: ?>
                    <?= Html::img(Yii::getAlias("@web/img/newbuilding-complex.png")) ?>
                <?php endif ?>
            </a>
        </div>
        <!--<?= Html::a('Подробнее', ['newbuilding-complex/view', 'id' => $model->id], [
            'class' => 'btn btn-white'
        ]) ?>-->
    </div>
</div>
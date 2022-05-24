<?php
use yii\helpers\Html;
use yii\helpers\Url;

$format = \Yii::$app->formatter;
?>

<div class="col-xs-6 col-sm-4 col-lg-3">
    <a href="<?= Url::to(['newbuilding-complex/view', 'id' => $model->id]) ?>">
        <!--<div style="margin-bottom: 15px;">-->
            <div class="nc-list--item">
                <div class="image">
                    <?php if(!is_null($model->logo)): ?>
                        <?= Html::img([Yii::getAlias("@web/uploads/{$model->logo}")]) ?>
                    <?php else: ?>
                        <?= Html::img([Yii::getAlias("@web/img/newbuilding-complex.png")]) ?>
                    <?php endif ?>
                </div>
                <p class="title">
                    <?= Html::a($model->name, ['newbuilding-complex/view', 'id' => $model->id]) ?>
                </p>        
                <!--<?= Html::a('Подробнее', ['newbuilding-complex/view', 'id' => $model->id], [
                    'class' => 'btn btn-white'
                ]) ?>-->
            </div>
        <!--</div>-->
    </a>
</div>
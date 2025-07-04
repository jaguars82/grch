<?php
/* @var $model app\models\Developer */

use yii\helpers\Html;
use yii\helpers\Url;

$format = \Yii::$app->formatter;
?>
<div class="col-xs-6 col-sm-4 col-lg-3">
    <a href="<?= Url::to(['developer/view', 'id' => $model->id]) ?>">
        <div class="developer-list--item hover-accent">
            <div class="image">
                <?php if(!is_null($model->logo)): ?>
                    <?= Html::img([Yii::getAlias("@web/uploads/{$model->logo}")]) ?>
                <?php else: ?>
                    <?= Html::img([Yii::getAlias("@web/img/developer.png")]) ?>
                <?php endif ?>
            </div>
            <p class="title">
                <?= Html::a($model->name, ['developer/view', 'id' => $model->id]) ?>
            </p>
            <div class="contacts">
                <?php if(!is_null($model->address)): ?>
                    <span class="location"><?= $model->address ?></span>
                <?php endif; ?>
                <?php if(!is_null($model->email)): ?>
                    <a href="mailto:<?= $model->email ?>" class="mail"><?= $model->email ?></a>
                <?php endif; ?>
                <?php if(!is_null($model->url)): ?>
                    <a href="mailto:<?= $model->email ?>" class="web"><?= $format->asHost($model->url) ?></a>
                <?php endif; ?>
                <?php if(!is_null($model->phone)): ?>
                    <span class="phone"><?= $model->phone ?></span>
                <?php endif; ?>
            </div>
            <!--<?= Html::a('Подробнее', ['developer/view', 'id' => $model->id], [
                'class' => 'btn btn-white'
            ]) ?>-->
        </div>
    </a>
</div>
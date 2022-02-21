<?php
use yii\helpers\Html;
use yii\helpers\Url;

$format = \Yii::$app->formatter;
?>
<div class="swiper-slide slider-card developer-slide">
    <div class="image">
        <a href="<?= Url::to(['developer/view', 'id' => $model->id]) ?>">
            <?php if(!is_null($model->logo)): ?>
                <?= Html::img([Yii::getAlias("@web/uploads/{$model->logo}")]); ?>
            <?php else: ?>
                <?= Html::img([Yii::getAlias("@web/img/developer.png")]); ?>
            <?php endif; ?>
        </a>
    </div>
    <p class="title">
        <?= Html::a($model->name, ['developer/view', 'id' => $model->id]) ?>
    </p>
    <div class="links border-top border-bottom">
        <span class="split-link">
            <span>Жилых комплексов</span>
            <span><?= $model->getNewbuildingComplexes()->onlyActive()->count() ?></span>
        </span>
        <span class="split-link">
            <span>Квартир в продаже</span>
            <span><?= $model->getFlats()->onlyActive()->count() ?></span>
        </span>
    </div>
    <span class="split-link">
        <span>Действующих акций</span>
        <span><?= $model->actionNumber ?></span>
    </span>
    <?php if(!is_null($model->detail)): ?>
    <p class="desc">
        <?= $format->asShortText($model->detail, 200, true) ?>
    </p>
    <?php endif; ?>
</div>
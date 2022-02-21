<?php
use yii\helpers\Html;

$format = \Yii::$app->formatter;
?>

<div style="margin-bottom: 20px">
    <h4 style="font-size: 17px; text-decoration: underline"><?= Html::a($model->title, ['news/view', 'id' => $model->id]) ?></h4>

    <p class="text-justify">
        <?= $format->asShortText($model->detail) ?>
    </p>

    <?php if(!is_null($model->search_link) && !empty($model->search_link)): ?>
        <?= Html::a('< Показать квартиры', $model->search_link) ?>
    <?php endif ?>
</div>

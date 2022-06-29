<?php
/* @var $model app\models\Developer */

use app\models\News;
use yii\helpers\Html;
use yii\helpers\Url;

$format = \Yii::$app->formatter;
?>
<div class="news-list--item">
    <div class="info">
        <span class="type <?= $model->isAction() ? 'stock' : 'news' ?>">
            <?= $model->isAction() ? 'Акция' : 'Новость' ?>
        </span>
        <span class="date">
            <?= $format->asDate($model->created_at, 'php:d.m.Y') ?>
        </span>
        <!--<?= Html::a($model->title, ['news/view', 'id' => $model->id], ['class' => 'group']) ?>-->
        <strong><?= $model->title ?></strong>
    </div>
    <div class="flex-row">
        <div class="desc">
            <?= $format->asHtml($model->detail) ?>
        </div>
        <div class="image">
            <?php if(!is_null($model->image)): ?>
                <?= Html::img([Yii::getAlias("@web/uploads/{$model->image}")]) ?>
            <?php else: ?>
                <?= Html::img([Yii::getAlias("@web/img/blank-news.svg")]) ?>
            <?php endif ?>
        </div>
    </div>
    
    <?php if(count($model->newsFiles)): ?>
        <?php foreach($model->newsFiles as $newsFile): ?>
            <a class="document-item" href="<?= Url::to(['news/download', 'id' => $newsFile->news_id, 'file' => $newsFile->saved_name]) ?>">
                <span class="title">
                    <?= $newsFile->name ?>
                </span>
                <span class="info">
                    <?= $newsFile->saved_name ?>
                </span>
            </a>
        <?php endforeach ?>
    <?php endif; ?>

    <?php if(!empty($model->search_link)): ?>
        <?= Html::a('Показать квартиры', $model->search_link, ['class' => 'btn btn-primary']) ?>
    <?php endif ?>
</div>
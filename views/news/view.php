<?php
/* @var $model app\models\News */
/* @var $this yii\web\View */

use app\models\News;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Новости', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$format = \Yii::$app->formatter;
\yii\web\YiiAsset::register($this);

if ($model->isAction()) {
   $flatfilter = json_decode($model->actionData->flat_filter); 
}

?>

<div class="news-view">
    <div class="row">
        <div class="col-md-9">
            <div class="white-block">
                <h3 class="bordered"><?= Html::encode($this->title) ?></h3>
                <div class="flex-row news-view--info">
                    <div class="properties">
                        <div class="properties-item">
                            <span>Дата размещения</span>
                            <b><?= $format->asRelativeTime($model->created_at) ?></b>
                        </div>
                        <div class="properties-item">
                            <span>Категория</span>
                            <b><?= News::$category[$model->category] ?></b>
                        </div>
                        <?php if(count($model->newbuildingComplexes) > 0): ?>
                            <div class="properties-item">
                                <span>ЗастройЩик</span>
                                <b>
                                    <?= Html::a($model->newbuildingComplexes[0]->developer->name, [
                                        'developer/view', 'id' => $model->newbuildingComplexes[0]->developer_id
                                    ]) ?>
                                </b>
                            </div>
                        <?php endif; ?>
                        <?php if(!is_null($model->actionData)): ?>
                            <div class="properties-item">
                                <span>Дата окончания</span>
                                <b><?= $format->asRelativeTime($model->actionData->expired_at) ?></b>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="image">
                        <?php if (!is_null($model->image)) : ?>
                            <?= Html::img([Yii::getAlias("@web/uploads/{$model->image}")]) ?>
                        <?php else : ?>
                            <?= Html::img([Yii::getAlias("@web/img/news.png")]) ?>
                        <?php endif ?>
                    </div>
                </div>

                <?php if(!empty($model->search_link)): ?>
                    <div class="labels">
                        <?= Html::a('Квартиры', $model->search_link, ['class' => 'btn btn-white']) ?>
                    </div>
                <?php endif; ?>
                <!--
                <div class="labels">
                    <a href="<?= Url::to([
                        'site/search', 
                        'AdvancedFlatSearch[roomsCount]' => $flatfilter->rooms,
                        //'AdvancedFlatSearch[flatType]' => AdvancedFlatSearch::FLAT_TYPE_STANDARD,
                        // 'AdvancedFlatSearch[newbuilding_complex]' => $flatfilter->newbuilding_complex,
                        'AdvancedFlatSearch[developer]' => $flatfilter->developer,
                    ]); ?>" class="link-list--item">Квартиры</a>
                </div>
                -->
                <?php if(!is_null($model->actionData)): ?>
                    <p class="h3 bordered">
                        Суть акции
                    </p>
                    <div class="toogle-desc">
                        <div class="toggle-desc-content">
                            <?= $model->actionData->resume ?>
                        </div>
                        <button class="toggle-desc--trigger"></button>
                    </div>
                <?php endif; ?>

                <?php if (!is_null($model->detail)) : ?>
                    <p class="h3 bordered">Описание</p>
                    <div class="toggle-desc">
                        <div class="toggle-desc--content">
                            <?= $format->asHtml($model->detail); ?>
                        </div>
                        <button class="toggle-desc--trigger"></button>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (count($model->newbuildingComplexes)) : ?>
                <div class="white-block nc-list">
                    <p class="h3 bordered">
                        Объекты, на которые распространяется:
                    </p>
                    <div class="delimiter-list flex-row row">
                        <?php foreach ($model->newbuildingComplexes as $newbuildingComplex) : ?>
                            <div class="col-xs-6 col-sm-4">
                                <div class="nc-list--item">
                                    <p class="title">
                                        <?= Html::a($format->asCapitalize($newbuildingComplex->name), ['newbuilding-complex/view', 'id' => $newbuildingComplex->id])?>
                                    </p>
                                    <div class="image">
                                        <a href="<?= Url::to(['newbuilding-complex/view', 'id' => $newbuildingComplex->id])?>">
                                            <?php if (!is_null($newbuildingComplex->logo)) : ?>
                                                <?= Html::img([Yii::getAlias("@web/uploads/{$newbuildingComplex->logo}")]) ?>
                                            <?php else : ?>
                                                <?= Html::img([Yii::getAlias("@web/img/newbuilding-complex.png")]) ?>
                                            <?php endif ?>
                                        </a>
                                    </div>
                                    <?= Html::a('Подробнее', ['newbuilding-complex/view', 'id' => $newbuildingComplex->id], ['class' => 'btn btn-white']) ?>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div>
                </div>
            <?php endif ?>

            <?php if (count($model->newsFiles)) : ?>
                <div class="white-block document-list">
                    <p class="h3 bordered">
                        Документы
                    </p>
                    <div class="document-list--content">
                        <?php foreach ($model->newsFiles as $newsFile) : ?>
                            <a class="document-list--item" href="<?= Url::to(['news/download', 'id' => $newsFile->news_id, 'file' => $newsFile->name]) ?>">
                                <span class="title">
                                    <?= $newsFile->name ?>
                                </span>
                                <span class="info">
                                    <?= $newsFile->saved_name ?>
                                </span>
                            </a>
                        <?php endforeach ?>
                    </div>
                    <span class="document-list--trigger"></span>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
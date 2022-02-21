<?php
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $this yii\web\View */

use app\assets\NewsIndexAsset;
use app\components\widgets\LoadMore;
use yii\helpers\Html;
use yii\widgets\ListView;
use yii\widgets\Pjax;

$this->title = 'Новости';
$this->params['breadcrumbs'][] = $this->title;

NewsIndexAsset::register($this);
?>


<div class="row flex-row">
    <div class="col-xs-12 col-md-8">
        <div class="news-list-buttons">
            <button class="btn btn-primary js-news-categories">
                Категории
            </button>
        </div>
        <?php Pjax::begin([
            'id' => 'news-content',
            'enablePushState' => false,
            'enableReplaceState' => false,
            'linkSelector' => 'a.dummy',
        ]); ?>
        <div class="news-list">
            <p class="h3">
                <h1><?= Html::encode($this->title) ?></h1>
            </p>
            <div class="news-tab-control">
                <span data-tab="all" class="news-tab-control--item all 
                    <?php if(strpos(Yii::$app->request->queryString, 'all-page') !== false
                            || preg_match('/^(developer|newbuilding-complex)=[0-9]+$/', Yii::$app->request->queryString)
                            || strpos(Yii::$app->request->url, '?') === false): 
                    ?>
                    active
                    <?php endif ?>">
                    Всё
                </span>
                <span data-tab="news" class="news-tab-control--item news
                    <?php if(strpos(Yii::$app->request->queryString, 'news-page') !== false): ?>
                    active
                    <?php endif ?>">
                    Новости
                </span>
                <span data-tab="actions" class="news-tab-control--item stock
                    <?php if(strpos(Yii::$app->request->queryString, 'action-page') !== false): ?>
                    active
                    <?php endif ?>">
                    Акции
                </span>
            </div>
            <div id="all-news-view" class="news-tab-content
                    <?php if(strpos(Yii::$app->request->queryString, 'all-page') !== false 
                    || preg_match('/^(developer|newbuilding-complex)=[0-9]+$/', Yii::$app->request->queryString)
                    || strpos(Yii::$app->request->url, '?') === false):?>
                    active
                    <?php endif ?>" data-tab="all">
                <?= ListView::widget([
                    'dataProvider' => $dataProvider,
                    'itemView' => '/common/_news-item',
                    'summary' => '',
                    'emptyText' => 'Новости и акции ещё не добавлены',
                    'layout'=>"{items}",
                    'itemOptions' => [
                        'tag' => false,
                    ],
                ]); ?>
            </div>
            <div id="news-view" class="news-tab-content
                <?php if(strpos(Yii::$app->request->queryString, 'news-page') !== false): ?>
                active
                <?php endif ?>" data-tab="news">
                <?= ListView::widget([
                    'dataProvider' => $newsDataProvider,
                    'itemView' => '/common/_news-item',
                    'summary' => '',
                    'emptyText' => 'Новости ещё не добавлены',
                    'layout'=>"{items}",
                    'itemOptions' => [
                        'tag' => false,
                    ],
                ]); ?>
            </div>
            <div id="actions-view" class="news-tab-content
                <?php if(strpos(Yii::$app->request->queryString, 'action-page') !== false): ?>
                active
                <?php endif ?>" data-tab="actions">
                <?= ListView::widget([
                    'dataProvider' => $actionsDataProvider,
                    'itemView' => '/common/_news-item',
                    'summary' => '',
                    'emptyText' => 'Акции ещё не добавлены',
                    'layout'=>"{items}",
                    'itemOptions' => [
                        'tag' => false,
                    ],
                ]); ?>
            </div>
        </div>
        <div data-tab="all" class="news-tab-pagination
            <?php if(strpos(Yii::$app->request->queryString, 'all-page') !== false 
            || preg_match('/^(developer|newbuilding-complex)=[0-9]+$/', Yii::$app->request->queryString)
            || strpos(Yii::$app->request->url, '?') === false):?>
            active
            <?php endif ?>">
            <?= \yii\widgets\LinkPager::widget([
                'pagination' => $dataProvider->pagination,
                'options' => [
                    'class' => 'pagination',
                ]
            ]); ?>
        </div>
        <div data-tab="news" class="news-tab-pagination
            <?php if(strpos(Yii::$app->request->queryString, 'news-page') !== false): ?>
            active
            <?php endif ?>">
            <?= \yii\widgets\LinkPager::widget([
                'pagination' => $newsDataProvider->pagination,
                'options' => [
                    'class' => 'pagination',
                ]
            ]); ?>
        </div>
        <div data-tab="actions" class="news-tab-pagination
            <?php if(strpos(Yii::$app->request->queryString, 'action-page') !== false): ?>
            active
            <?php endif ?>">
            <?= \yii\widgets\LinkPager::widget([
                'pagination' => $actionsDataProvider->pagination,
                'options' => [
                    'class' => 'pagination',
                ]
            ]); ?>
        </div>
        <?php Pjax::end(); ?>
    </div>
    <div class="col-md-4">
        <div class="sticky news-categories">
            <span class="mobile-close"></span>
            <div class="sidebar white-block scrollbar">
                <p class="h3 bordered">
                    Группы новостей
                </p>
                <?= ListView::widget([
                    'dataProvider' => $newbuildingComplexesDataProvider,
                    'viewParams' => [
                        'newbuildingComplexId' => isset(\Yii::$app->request->queryParams['newbuilding-complex']) ? \Yii::$app->request->queryParams['newbuilding-complex'] : null
                    ],
                    'itemView' => '/common/_newbuilding-complex-list',
                    'beforeItem' => function ($model, $key, $index, $widget) { 
                        if ($index  == 0) {
                            return '<div class="news-categories--item">' . Html::a('Все', ['/news']) . '</div>';
                        }
                    },
                    'summary' => '',
                    'emptyText' => 'Данные по жилые комплексам отсутсвуют'
                ]); ?>
            </div>
        </div>
    </div>
</div>
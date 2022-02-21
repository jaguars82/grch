<?php
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $this yii\web\View */

use app\models\News;
use app\assets\NewsIndexAsset;
use yii\helpers\Html;
use yii\widgets\ListView;
use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = 'Новости';
$this->params['breadcrumbs'][] = ['label' => 'Администрированние', 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = $this->title;

NewsIndexAsset::register($this);
?>

<div class="news-index white-block">
    <h2 class="bordered"><?= Html::encode($this->title) . (!is_null($searchObject) ? " для $searchObject" : '') ?></h2>

    <?php if(\Yii::$app->user->can('admin')): ?>
        <p><?= Html::a('Добавить', ['create'], ['class' => 'btn btn-success']) ?></p>
    <?php endif ?>
    
    <div class="row">
        <div class="col-xs-12 col-md-8">
            <?php Pjax::begin([
                'id' => 'news-content',
                'enablePushState' => true,
                'enableReplaceState' => false,
                'linkSelector' => 'a.dummy',
            ]); ?>
            <ul class="developer-data-switch nav nav-pills" role="tablist" style="margin-top: 10px">
                <li role="presentation" 
                    <?php if(strpos(Yii::$app->request->queryString, 'all-page') !== false
                            || preg_match('/^(developer|newbuilding-complex)=[0-9]+$/', Yii::$app->request->queryString)
                            || strpos(Yii::$app->request->url, '?') === false): 
                    ?>
                    class="active"
                    <?php endif ?>
                ><a href="#all" role="tab" data-toggle="tab">Всё</a></li>
                
                <li role="presentation" 
                    <?php if(strpos(Yii::$app->request->queryString, 'news-page') !== false): ?>
                    class="active"
                    <?php endif ?>
                ><a href="#news" role="tab" data-toggle="tab">Новости</a></li>
                
                <li role="presentation" 
                    <?php if(strpos(Yii::$app->request->queryString, 'action-page') !== false): ?>
                    class="active"
                    <?php endif ?>
                ><a href="#actions" role="tab" data-toggle="tab">Акции</a></li>
            </ul>

            
            
            <div id="data-wrap">
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane fade
                    <?php if(strpos(Yii::$app->request->queryString, 'all-page') !== false 
                            || preg_match('/^(developer|newbuilding-complex)=[0-9]+$/', Yii::$app->request->queryString)
                            || strpos(Yii::$app->request->url, '?') === false):
                        ?>
                    in
                    active
                    <?php endif ?>" 
                id="all">
                    <div id="all-news-view">
                        <?= GridView::widget([
                            'dataProvider' => $dataProvider,
                            'columns' => [
                                ['class' => 'yii\grid\SerialColumn', 'header' => ''],
                                [
                                    'attribute' => 'image',
                                    'content' => function ($model, $key, $index, $column) {
                                        if(!is_null($model->image)) {
                                            return Html::img([Yii::getAlias("@web/uploads/{$model->image}")], ['style' => 'max-width: 100%; max-height: 50px;']);
                                        } else {
                                            return Html::img([Yii::getAlias("@web/img/news.png")], ['height' => 50]);
                                        }
                                    }
                                ],
                                [
                                    'attribute' => 'title',
                                    'enableSorting' => false,
                                    'content' => function ($model, $key, $index, $column) {
                                        return  Html::a($model->title, ['update', 'id' => $model->id]);
                                    }
                                ],
                                [
                                    'attribute' => 'category',
                                    'content' => function ($model, $key, $index, $column) {
                                        return News::$category[$model->category];
                                    }
                                ],
                                [
                                    'attribute' => 'created_at',
                                    'format' => ['date', 'php:Y-m-d']
                                ],
                                ['class' => 'yii\grid\ActionColumn', 'visibleButtons' => ['view' => false]],
                            ],
                            'summary' => '',
                            'emptyText' => 'Новости и акции ещё не добавлены', 
                        ]); ?>
                    </div>
                </div>
            
                <div role="tabpanel" class="tab-pane fade
                    <?php if(strpos(Yii::$app->request->queryString, 'news-page') !== false): ?>
                    in
                    active
                    <?php endif ?>" 
                id="news">
                    <div id="news-view">
                        <?= GridView::widget([
                            'dataProvider' => $newsDataProvider,
                            'columns' => [
                                ['class' => 'yii\grid\SerialColumn', 'header' => ''],
                                [
                                    'attribute' => 'image',
                                    'content' => function ($model, $key, $index, $column) {
                                        if(!is_null($model->image)) {
                                            return Html::img([Yii::getAlias("@web/uploads/{$model->image}")], ['style' => 'max-width: 100%; max-height: 50px;']);
                                        } else {
                                            return Html::img([Yii::getAlias("@web/img/news.png")], ['height' => 50]);
                                        }
                                    }
                                ],
                                [
                                    'attribute' => 'title',
                                    'enableSorting' => false,
                                    'content' => function ($model, $key, $index, $column) {
                                        return  Html::a($model->title, ['update', 'id' => $model->id]);
                                    }
                                ],
                                [
                                    'attribute' => 'category',
                                    'content' => function ($model, $key, $index, $column) {
                                        return News::$category[$model->category];
                                    }
                                ],
                                [
                                    'attribute' => 'created_at',
                                    'format' => ['date', 'php:Y-m-d']
                                ],
                                ['class' => 'yii\grid\ActionColumn', 'visibleButtons' => ['view' => false]],
                            ],
                            'summary' => '',
                            'emptyText' => 'Новости ещё не добавлены',
                        ]); ?>
                    </div>
                </div>

                <div role="tabpanel" class="tab-pane fade
                    <?php if(strpos(Yii::$app->request->queryString, 'action-page') !== false): ?>
                    in
                    active
                    <?php endif ?>" 
                id="actions">
                    <div id="actions-view">
                        <?= GridView::widget([
                            'dataProvider' => $actionsDataProvider,
                            'columns' => [
                                ['class' => 'yii\grid\SerialColumn', 'header' => ''],
                                [
                                    'attribute' => 'image',
                                    'content' => function ($model, $key, $index, $column) {
                                        if(!is_null($model->image)) {
                                            return Html::img([Yii::getAlias("@web/uploads/{$model->image}")], ['style' => 'max-width: 100%; max-height: 50px;']);
                                        } else {
                                            return Html::img([Yii::getAlias("@web/img/news.png")], ['height' => 50]);
                                        }
                                    }
                                ],
                                [
                                    'attribute' => 'title',
                                    'enableSorting' => false,
                                    'content' => function ($model, $key, $index, $column) {
                                        return  Html::a($model->title, ['update', 'id' => $model->id]);
                                    }
                                ],
                                [
                                    'attribute' => 'category',
                                    'content' => function ($model, $key, $index, $column) {
                                        return News::$category[$model->category];
                                    }
                                ],
                                [
                                    'attribute' => 'created_at',
                                    'format' => ['date', 'php:Y-m-d']
                                ],
                                ['class' => 'yii\grid\ActionColumn', 'visibleButtons' => ['view' => false]],
                            ],
                            'summary' => '',
                            'emptyText' => 'Акции ещё не добавлены',
                        ]); ?>
                    </div>
                </div>
            </div>
            </div>
            
            <?php Pjax::end(); ?>
        </div>
        
        <div class="col-xs-12 col-md-4">
            <h3 style="margin-bottom: 37px">Группы новостей</h3>
            <div class="panel panel-default">
                <div class="panel-body">
                    <?= ListView::widget([
                        'dataProvider' => $newbuildingComplexesDataProvider,
                        'itemView' => '_item_newbuilding_complexes',
                        'beforeItem' => function ($model, $key, $index, $widget) { 
                            if ($index  == 0) {
                                return Html::a('Все', ['/admin/news/index']);
                            }                 
                        },
                        'summary' => '',
                        'emptyText' => 'Данные по жилые комплексам отсутсвуют'
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

use app\assets\OfferIndexAsset;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

$this->title = 'КП';
$this->params['breadcrumbs'][] = $this->title;
$format = \Yii::$app->formatter;

OfferIndexAsset::register($this);
?>

<div class="offer-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_search', [
        'model' => $searchModel, 
    ]) ?>
    
    <?php Pjax::begin([
        'id' => 'search-result',
        'enablePushState' => true,
        'enableReplaceState' => false,
        'linkSelector' => 'a.dummy',
    ]); ?>
    
    <div id="search-result-count" style="margin-bottom: 15px">
        Найдено КП: <?= $itemsCount ?>
    </div>
    
    <div id="data-wrap">        
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'summary' => '',
            'columns' => [
                [
                    'class' => 'yii\grid\ActionColumn',
                    'header' => 'КП',
                    'template' => '{update}',
                    'contentOptions' => [ 'class' => 'text-center', 'style' => 'width: 30px' ],
                ],
                [
                    'attribute' => 'url',
                    'value' => function ($offer) use ($format) {
                        $url = \Yii::$app->request->hostInfo . Url::to(['offer/view', 'id' => $offer->url]);
                        return Html::a($url, $url, ['target' => '_blank']);
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'created_at',
                    'contentOptions' => [ 'style' => 'width: 300px' ],
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view}{delete}',
                    'buttons'=>[
                        'view' => function ($url, $model) {
                            return Html::a('<span class="glyphicon glyphicon-home" style="margin-left: 5px"></span>', 
                                Url::to(['/flat/view', 'id' => $model->flat_id]),
                                ['title' => 'Карточка объекта', 'target' => '_blank']
                            );
                        },
                        'delete' => function ($url, $model) {	
                            return Html::a('<span class="glyphicon glyphicon-trash" style="margin-left: 5px"></span>', 'javascript:void(0);', [
                                'class' => 'delete-offer',
                                'title' => 'Удалить КП',
                                'data' => [
                                    'target' => Url::to(['offer/delete', 'id' => $model->id]),
                                    'method' => 'post'
                                ]
                            ]);                                
                        }
                    ],
                    'contentOptions' => [ 'class' => 'text-center', 'style' => 'width: 80px' ],
                ],
            ],
        ]); ?>        
    </div>
    
    <?php Pjax::end(); ?>
</div>

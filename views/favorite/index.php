<?php
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

use app\assets\FavoriteIndexAsset;
use app\components\widgets\ImageView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ListView;
use yii\widgets\Pjax;

$this->title = 'Избранное';
$this->params['breadcrumbs'][] = $this->title;

FavoriteIndexAsset::register($this);
?>

<div class="row flex-row">
    <div class="col-xs-12 col-md-8">
        <ul class="favorite-switch nav nav-pills" role="tablist">
            <li role="presentation" class="active"><a href="#active" role="tab" data-toggle="tab">Актуальное</a></li>
            <li role="presentation"><a href="#archive" role="tab" data-toggle="tab">Архив</a></li>
        </ul>
        
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane fade in active" id="active">
                <?php Pjax::begin([
                    'id' => 'active-favorites',
                    'enablePushState' => true,
                    'enableReplaceState' => false,
                ]); ?>

                <div class="flat-list" id="data-wrap">
                    <?= ListView::widget([
                        'dataProvider' => $activeDataProvider,
                        'itemView' => '_item',
                        'itemOptions' => [
                            'tag' => false,
                        ],
                        'summary' => '',
                        'layout' => "{items}\n{pager}",
                        'pager' => ['options' => ['class' => 'pagination active-favorite-pagination']],
                        'emptyText' => 'Данные о квартирах отсутсвуют'
                    ]); ?>         
                </div>
                
                <?php Pjax::end(); ?>
            </div>

            <div role="tabpanel" class="tab-pane fade" id="archive">
                <?php Pjax::begin([
                    'id' => 'archive-favorites',
                    'enablePushState' => true,
                    'enableReplaceState' => false,
                ]); ?>
                
                <?php if($archiveDataProvider->totalCount): ?>
                    <?= Html::a('Удалить все',
                        //['delete-all-archived'],
                        'javascript:void(0);',
                        [
                            'class' => 'btn btn-danger delete-all-archived',
                            'data' => [
                                'target' => Url::to(['favorite/delete-all-archived']),
                                //'confirm' => 'Вы уверены, что хотите удалить все избранное в архиве?',
                                'method' => 'post',
                            ],
                        ]) 
                    ?>
                <?php endif ?>
                
                <div class="flat-list" id="data-wrap"> 
                    <?= ListView::widget([
                        'dataProvider' => $archiveDataProvider,
                        'itemView' => '_item',
                        'itemOptions' => [
                            'tag' => false,
                        ],
                        'summary' => '',
                        'layout' => "{items}\n{pager}",
                        'pager' => ['options' => ['class' => 'pagination archive-favorite-pagination']],
                        'emptyText' => 'Данные о квартирах отсутсвуют'
                    ]); ?>
                </div>

                <?php Pjax::end(); ?>
            </div>
        </div>
    </div>
    <div class="col-md-4">
         <div class="sticky js-search-filter flat-list-sidebar">
            <button class="btn btn-primary js-flat-search">Поиск</button>
            <?= $this->render('_search', [
                'model' => $searchModel,
                'regions' => $regions,
                'cities' => $cities,
                'districts' => $districts,
                'developers' => $developers
            ]) ?>
        </div>
    </div>
</div>
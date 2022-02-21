<?php
/* @var $this yii\web\View */

use app\assets\SiteSearchAsset;
use app\components\widgets\ImageView;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ListView;
use yii\widgets\Pjax;

$this->title = 'Расширенный поиск';
$this->params['breadcrumbs'][] = $this->title;

$format = \Yii::$app->formatter;
SiteSearchAsset::register($this);
?>

<div class="row flex-row">
    <div class="col-xs-12 col-md-8">
        <div class="flat-list-buttons">
            <a href="#" class="btn btn-white js-map-search">Показать на карте</a>
        </div>
        <div class="flat-list">
            <?php Pjax::begin([
                'id' => 'search-result',
                'enablePushState' => true,
                'enableReplaceState' => false,
                'linkSelector' => 'a.dummy',
            ]); ?>
            <?= ListView::widget([
                'dataProvider' => $dataProvider,
                'itemView' => '/common/_flat-list-item',
                'layout' => "{items}\n{pager}",
                'emptyText' => 'Данные по квартирам отсутсвуют',
                'itemOptions' => [
                    'tag' => false,
                ]
            ]); ?>
            <?php Pjax::end(); ?>
        </div>
    </div>
    <div class="col-md-4">
         <div class="sticky js-search-filter flat-list-sidebar">
            <button class="btn btn-primary js-flat-search">Поиск</button>
            <?= $this->render('/common/_advanced-search', [
                'model' => $searchModel, 
                'districts' => $districts,
                'developers' => $developers,
                'newbuildingComplexes' => $newbuildingComplexes,
                'positionArray' => $positionArray,
                'materials' => $materials,
                'regions' => $regions,
                'cities' => $cities,
                'districts' => $districts,
                'deadlineYears' => $deadlineYears,
                'id' => 'advanced-search'
            ]) ?>
        </div>
    </div>
</div>
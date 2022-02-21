<?php
/* @var $this yii\web\View */

use app\assets\SiteMapAsset;
use yii\helpers\Html;

$this->title = 'Поиск по карте';
$this->params['breadcrumbs'][] = $this->title;

SiteMapAsset::register($this);
?>

<div class="flex-row search-map">
    <div class="search-map--buttons">
        <a href="#" class="btn btn-white js-main-search">
            Показать списком
        </a>
    </div>

    <div class="search-map--content" data-latitude="<?= $selectedCity->latitude ?>" data-longitude="<?= $selectedCity->longitude ?>" id="map-content"></div>
    <div class="search-map--sidebar js-search-filter">
        <?= Html::a('Поиск', 'javascript:void(0);', ['class' => 'btn btn-primary js-flat-search']) ?>
        <?= $this->render('/common/_advanced-search', [
            'model' => $searchModel,
            'regions' => $regions,
            'cities' => $cities,
            'districts' => $districts,
            'developers' => $developers,
            'newbuildingComplexes' => $newbuildingComplexes,
            'positionArray' => $positionArray,
            'materials' => $materials,
            'deadlineYears' => $deadlineYears,
            'id' => 'map-search'
        ]) ?>
    </div>
    
    <div class="alert-container">
        <div class="alert alert-danger text-center not-found-alert-template" role="alert" style="display: none;">
            <span style="">Ничего не найдено</span>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close >
                    <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
</div>

<!-- 
<div class="alert alert-info" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    Чтобы произвести поиск должен быть выбран хотя бы один критерий поиска(застройщик, стоимость и т.п.). Выделение области поиска на карте осуществляется с зажатой правой кнопкой мыши, затем нужно выбрать критерии поиска и нажать на кнопку "Поиск". Если область не выделена, то поиск будет производиться по всей карте. За дополнительной информацией обратитесь в техническую поддержку.
</div>
-->
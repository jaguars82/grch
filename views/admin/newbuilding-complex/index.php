<?php
/* @var $this yii\web\View */

use app\assets\NewbuildingComplexIndexAsset;
use yii\helpers\Html;
use yii\widgets\ListView;
use yii\widgets\Pjax;

$this->title = 'Жилые комплексы';
$this->params['breadcrumbs'][] = ['label' => 'Администрированние', 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = $this->title;

NewbuildingComplexIndexAsset::register($this);
?>

<div class="newbuilding-complex-index white-block">
    <h2 class="bordered"><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_search', [
        'model' => $searchModel, 
        'dataProvider' => $dataProvider,
        'developers' => $developers,
    ]) ?>

    <?php Pjax::begin([
        'id' => 'search-result',
        'enablePushState' => true,
        'enableReplaceState' => false,
    ]); ?>
    
    <div id="search-result-count" style="margin-bottom: 15px">
        Найдено жилых комплексов: <?= $itemsCount ?>
    </div>
    
    <div id="data-wrap">
    
    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'itemView' => '_item',
        'summary' => '',
        'emptyText' => 'Данные о жилых комплексах отсутсвуют'
    ]); ?>
        
    </div>
    
    <?php Pjax::end(); ?>
</div>

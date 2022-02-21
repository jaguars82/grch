<?php
/* @var $this yii\web\View */

use app\assets\NewbuildingComplexIndexAsset;
use yii\helpers\Html;
use yii\widgets\ListView;
use yii\widgets\Pjax;

$this->title = 'Жилые комплексы';
$this->params['breadcrumbs'][] = $this->title;

NewbuildingComplexIndexAsset::register($this);
?>

<div class="newbuilding-complex-index">
    <h3><?= Html::encode($this->title) ?></h3>
    
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
    
    <div id="data-wrap" class="nc-list-main">
        <?= ListView::widget([
            'dataProvider' => $dataProvider,
            'itemView' => '_item',
            'itemOptions' => [
                'tag' => false
            ],
            'summary' => '',
            'emptyText' => 'Данные о жилых комплексах отсутсвуют'
        ]); ?>
    </div>
    
    <?php Pjax::end(); ?>
</div>

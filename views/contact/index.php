<?php
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\widgets\ListView;
use yii\widgets\Pjax;

$this->title = 'Контакты';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="contact-index">
    <h1><?= Html::encode($this->title) ?></h1>
    
    <?= $this->render('_search', ['model' => $searchModel]) ?>
    
    <?php Pjax::begin([
        'id' => 'search-result',
        'enablePushState' => true,
        'enableReplaceState' => false,
    ]); ?>
    
    <div id="search-result-count" style="margin-bottom: 15px">
        Найдено агенств недвижимости: <?= $itemsCount ?>
    </div>
    
    <div id="data-wrap">
    
    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'itemView' => '_item',
        'summary' => '',
        'emptyText' => 'Данные о контактах отсутсвуют'
    ]); ?>
        
    </div>
    
    <?php Pjax::end(); ?>
</div>
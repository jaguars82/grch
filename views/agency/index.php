<?php
/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\widgets\ListView;
use yii\widgets\Pjax;

$this->title = 'Агентства недвижимости';
$this->params['breadcrumbs'][] = 'Агентства недвижимости';
?>

<div class="agency-list">
    <h3>
        <?= Html::encode($this->title) ?>
    </h3>
    
    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'itemView' => '/common/_agency-item',
        'itemOptions' => [
            'tag' => false,
        ],
        'options' => [
            'tag' => false
        ],
        'layout' => '<div class="row flex-row">{items}</div>{pager}',
        'viewParams' => [
            'colClass' => 'col-xs-6 col-lg-3'
        ],
        'summary' => '',
        'emptyText' => 'Данные о застройщиках отсутсвуют'
    ]);  ?>
</div>
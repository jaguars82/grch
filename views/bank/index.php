<?php
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\widgets\ListView;
use yii\widgets\Pjax;

$this->title = 'Банки';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="bank-list">
    <h3>
        <?= Html::encode($this->title) ?>
    </h3>
    
    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'itemView' => '/common/_bank-item',
        'itemOptions' => [
            'tag' => false,
        ],
        'options' => [
            'tag' => false
        ],
        'layout' => '<div class="row flex-row">{items}</div>{pager}',
        'viewParams' => [
            'colClass' => 'col-xs-6 col-sm-4 col-lg-3'
        ],
        'summary' => '',
        'emptyText' => 'Данные о застройщиках отсутсвуют'
    ]); ?>
</div>
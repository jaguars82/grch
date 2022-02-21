<?php
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\widgets\ListView;
use yii\widgets\Pjax;

$this->title = 'Застройщики';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="developer-list">
    <h3>
        Застройщики
    </h3>
    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'itemView' => '_item',
        'itemOptions' => [
            'tag' => false,
        ],
        'options' => [
            'tag' => false
        ],
        'layout' => '<div class="row flex-row">{items}</div>{pager}',
        'summary' => '',
        'emptyText' => 'Данные о застройщиках отсутсвуют'
    ]);  ?>
</div>
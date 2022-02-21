<?php
/* @var $model app\models\Agency */

use yii\helpers\Url;
use yii\widgets\ListView;
?>

<div>
    <h2>
        <a href="<?= Url::to(['agency/view', 'id' => $model['id']]) ?>"><?= $model['name'] ?></a>
    </h2>
</div>

<div class="row" style="margin-top: 20px">
    <?= ListView::widget([
        'dataProvider' => $model['provider'],
        'itemView' => '/common/_item-contact',
        'viewParams' => ['itemContactClass' => 'col-md-4', 'noControls' => true],
        'summary' => '',
        'emptyText' => 'Данные о контактах отсутсвуют'
    ]);  ?>
</div>
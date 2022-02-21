<?php
/* @var $model app\models\Developer */

use yii\helpers\Url;
use yii\widgets\ListView;
?>

<h3>
    <a href="<?= Url::to(['developer/view', 'id' => $model['id']]) ?>"><?= $model['name'] ?></a>
</h3>

<?= ListView::widget([
    'dataProvider' => $model['provider'],
    'itemView' => '_item-newbuilding-complex',
    'options' => [
        'class' => 'row flex-row'
    ],
    'itemOptions' => [
        'tag' => false
    ],
    'summary' => '',
    'emptyText' => 'Жилые комплексы ещё не добавлены'
]);  ?>
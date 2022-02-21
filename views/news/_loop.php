<?php
use yii\widgets\ListView;
?>

<?= ListView::widget([
    'dataProvider' => $dataProvider,
    'itemView' => '/common/_news-item',
    'summary' => '',
    'emptyText' => 'Новости и акции ещё не добавлены',
    'layout'=>"{items}",
    'itemOptions' => [
        'tag' => false,
    ],
]); ?>
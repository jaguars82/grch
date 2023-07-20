<?php
/* @var $model app\models\News */
/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Добавить новость';
$this->params['breadcrumbs'][] = ['label' => 'Администрированние', 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = ['label' => 'Новости', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Добавить';
?>

<div class="news-create white-block">
    <h2 class="bordered"><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_form', [
        'news' => $news,
        'action' => $action,
        'search' => $search,
        'developers' => $developers,
        'developersSearch' => $developersSearch,
        'districts' => $districts,
        'newbuildingComplexes' => $newbuildingComplexes,
        'newbuildings' => $newbuildings,
        'entrances' => $entrances,
        'index_on_floor' => $index_on_floor,
        'numbers' => $numbers,
        'positionArray' => $positionArray,
        'materials' => $materials,
        'backUrl' => ['news/index'],
    ]) ?>
</div>

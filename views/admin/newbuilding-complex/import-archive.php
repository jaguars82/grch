<?php
/* @var $model app\models\NewbuildingComplex */
/* @var $this yii\web\View */

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Pjax;
use app\components\widgets\FileInputButton;
use app\components\archive\services\ZipService;
use app\models\Newbuilding;
use app\models\Archive;

$this->title = 'Результат импорта';
$this->params['breadcrumbs'][] = ['label' => 'Администрированние', 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = ['label' => 'Застройщики', 'url' => ['admin/developer/index']];
$this->params['breadcrumbs'][] = ['label' => $developer->name, 'url' => ['admin/developer/update', 'id' => $developer->id]];
$this->params['breadcrumbs'][] = ['label' => 'Жилые комплексы', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['update', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="white-block">
    <?php foreach($result as $position => $layoutData):?>
        <h2><?= $position ?></h2>
        <?php foreach($layoutData as $layout): ?>
            <h4><?= $layout['section'] ?>, этаж с <?= $layout['floor_range'][0] ?> по  <?= $layout['floor_range'][1] ?></h4>
            <div class="layout-result" style="margin: 30px 0;">
                <?= $layout['html_layout'] ?>
            </div>
        <?php endforeach; ?>
    <?php endforeach; ?> 
</div>
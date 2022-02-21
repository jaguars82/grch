<?php
/* @var $developerId integer */
/* @var $model app\models\Developer */
/* @var $this yii\web\View */

use yii\helpers\Html;
use app\models\Import;
use yii\helpers\Url;
use app\components\widgets\FileInputButton;

$this->title = 'Обновить застройщика: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Администрированние', 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = ['label' => 'Застройщики', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['update', 'id' => $developerId]];
$this->params['breadcrumbs'][] = 'Обновить';
?>
<div class="developer-update white-block">
    <h2 class="bordered"><?= Html::encode($this->title) ?></h2>

    <div style="padding: 20px 0;">
        <?= Html::a('Контакты', ['admin/contact/developer-contact/index', 'developerId' => $developerId],  ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Жилые комплексы', ['/admin/newbuilding-complex/index', 'NewbuildingComplexSearch[developer_id]' => $developerId], ['class' => 'btn btn-primary']); ?>
        <?= Html::a('Офисы', ['/admin/office/developer-office/index', 'developerId' => $developerId], ['class' => 'btn btn-primary']); ?>

        <?php if($developer->hasImport()): ?>
            <?php if($developer->import->type === Import::IMPORT_TYPE_MANUAL): ?>
                <?= FileInputButton::widget([
                    'name' => 'Импорт',
                    'model' => $import,
                    'field' => 'file',
                    //'url' => ['import-manual', 'id' => $model->id],
                    'url' => ['import/for-developer', 'developerId' => $developer->id],
                    'container' => '#developer-view-block',
                    'reinit' => false,
                ]) ?>
            <?php elseif($developer->import->type === Import::IMPORT_TYPE_AUTO): ?>
                <?= Html::a('Импорт', 
                        //['import/for-developer', 'developerId' => $model->id, 'useEndpoint' => true],
                        'javascript:void(0);',
                        [
                            'class' => 'btn btn-primary import-auto',
                            'data' => [
                                //'target' => Url::to(['import-auto', 'id' => $model->id]),
                                'target' => Url::to([
                                    'import/for-developer',
                                    'developerId' => $developer->id,
                                    'useEndpoint' => true,
                                ]),
                            ],
                        ]
                ) ?>
            <?php endif ?>
        <?php endif ?>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
        'import' => $import,
        'backUrl' => ['admin/developer/index']
    ]) ?>
</div>

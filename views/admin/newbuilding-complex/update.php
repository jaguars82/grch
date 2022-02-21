<?php
/* @var $model app\models\NewbuildingComplex */
/* @var $this yii\web\View */

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Pjax;
use app\components\widgets\ImportInputButton;
use app\components\widgets\FileInputButton;

$this->title = 'Обновить жилой комплекс: ' . $newbuildingComplex->name;
$this->params['breadcrumbs'][] = ['label' => 'Администрированние', 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = ['label' => 'Застройщики', 'url' => ['admin/developer/index']];
$this->params['breadcrumbs'][] = ['label' => $developer->name, 'url' => ['admin/developer/update', 'id' => $developer->id]];
$this->params['breadcrumbs'][] = ['label' => 'Жилые комплексы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $newbuildingComplex->name;
 ?>

<div class="newbuilding-complex-update white-block">
    <h2 class="bordered"><?= Html::encode($this->title) ?></h2>

    <div class="bordered" style="margin-top: 20px; margin-bottom: 20px;">
        <?php Pjax::begin([
            'id' => 'import-archive-block',
            'enablePushState' => true,
            'enableReplaceState' => false,
            'linkSelector' => 'a.dummy',
            'options' => [
                'style' => 'display: inline-block'
            ]
        ]); ?>

        <?= ImportInputButton::widget([
                'name' => !empty($model->archive) ? 'Добавить новый архив' : 'Добавить архив',
                'model' => $archiveForm,
                'field' => 'file',
                'url' => ['upload-archive', 'newbuildingComplexId' => $newbuildingComplexId],
                'container' => '#import-archive-block',
            ]);
        ?>
        <?= Html::a('Отделка', ['/admin/furnish/index', 'newbuildingComplexId' => $newbuildingComplexId], ['class' => 'btn btn-primary']); ?>
        <?= Html::a('Позиции', ['/admin/newbuilding/index', 'newbuildingComplexId' => $newbuildingComplexId], ['class' => 'btn btn-primary']); ?>
        <?= Html::a('Контакты', ['/admin/contact/newbuilding-complex-contact/index', 'newbuildingComplexId' => $newbuildingComplexId], ['class' => 'btn btn-primary']); ?>
        <?= Html::a('Документы', ['/admin/document/newbuilding-complex-document/index', 'newbuildingComplexId' => $newbuildingComplexId], ['class' => 'btn btn-primary']); ?>

        <?php if(!empty($model->archive)): ?>
            <div>
                <?php if($canImportArchive): ?>
                    <?= Html::a('Импорт', ['import-archive', 'newbuildingComplexId' => $newbuildingComplexId], ['class' => 'btn btn-primary']); ?>
                    <br>
                <?php endif;?>
                <a href="<?= Url::to(['download-archive', 'newbuildingComplexId' => $newbuildingComplexId]) ?>" style="line-height: 30px">
                    Скачать архив (добавлен: <?= $model->archive->updated_at ?>, проверка: <?= $model->archive->checked ? 'пройдена' : 'не пройдена' ?>)
                </a>
            </div>
        <?php endif ?>
        
        <?php if(($errors = Yii::$app->session->getFlash('archive_error'))): ?>
            <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <?= $errors ?>
            </div>
        <?php endif; ?>
        
        <?php Pjax::end(); ?>
    </div>

    <?= $this->render('_form', [
        'newbuildingComplex' => $newbuildingComplex,
        'banks' => $banks,
        'bankTariffs' => $bankTariffs,
        'regions' => $regions,
        'cities' => $cities,
        'streetTypes' => $streetTypes,
        'buildingTypes' => $buildingTypes,
        'advantages' => $advantages,
        'savedImages' => $savedImages,
        'backUrl' => ['admin/newbuilding-complex/index',  'NewbuildingComplexSearch[developer_id]' => $developer->id]
    ]) ?>
</div>

<?php
/* @var $model app\models\Developer */
/* @var $this yii\web\View */

use app\components\widgets\ImageView;
use app\components\widgets\FileInputButton;
use app\components\widgets\Placemark;
use app\models\Import;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ListView;
use yii\widgets\Pjax;

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Застройщики', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$format = \Yii::$app->formatter;
\yii\web\YiiAsset::register($this);
?>

<div class="row flex-row developer-card">
    <div class="col-xs-12 col-md-8">
        <div class="white-block description">
            <h2 class="bordered">
                <?= Html::encode($model->name) ?>
            </h2>
            <div class="flex-row contacts-block">
                <?= $this->render('/common/_contact-block', [
                    'model' => $model,
                    'class' => 'content'
                ])?>
                <div class="image">
                    <?php if(!is_null($model->logo)): ?>
                        <?= Html::img([Yii::getAlias("@web/uploads/{$model->logo}") ]) ?>
                    <?php else: ?>
                        <?= Html::img([Yii::getAlias("@web/img/developer.png")]) ?>
                    <?php endif ?>
                </div>
            </div>
            
            <p class="h3 bordered">
                Условия бесплатной брони
            </p>
            <?php if(!empty($model->free_reservation)): ?>
                <?= $format->asHtml($model->free_reservation) ?>
            <?php else: ?>
                Не задано
            <?php endif ?>

            <p class="h3 bordered">
                Условия платной брони
            </p>
            <?php if(!empty($model->paid_reservation)): ?>
                <?= $format->asHtml($model->paid_reservation) ?>
            <?php else: ?>
                Не задано
            <?php endif ?>
            <?php if(!is_null($model->detail)): ?>
            <p class="h3 bordered">
                Описание
            </p>
            <div class="toggle-desc">
                <div class="toggle-desc--content">
                    <?= $format->asHtml($model->detail) ?>
                </div>
                <button class="toggle-desc--trigger"></button>
            </div>
            <?php endif; ?>
        </div>
        
        <?= Placemark::widget([
            'address' => $model->address,
            'longitude' => $model->longitude,
            'latitude' => $model->latitude
        ]) ?>
        
        <?php if($newsDataProvider->totalCount > 0): ?>
            <div class="inline-list">
                <p class="h3 bordered">Новости / Акции</p>
                <?= ListView::widget([
                    'dataProvider' => $newsDataProvider,
                    'itemView' => '/common/_inline-list-item',
                    'summary' => '',
                    'itemOptions' => [
                        'tag' => false
                    ],
                    'viewParams' => [
                        'displayType' => true
                    ],
                    'emptyText' => 'Новостей ещё нет'
                ]); ?>
                <?php if($newsDataProvider->totalCount > 3): ?>
                <div class="center">
                    <?= Html::a('Все новости Застройщика', ['/news', ['developer' => $model->id]]) ?>
                </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <?php if($newbuildingComplexDataProvider->totalCount > 0): ?>
            <div class="white-block nc-list">
                <p class="h3 bordered">
                    ЖК этого застройщика
                </p>
                <?= ListView::widget([
                    'dataProvider' => $newbuildingComplexDataProvider,
                    'itemView' => '/common/_newbuilding-complex-item',
                    'summary' => '',
                    'emptyText' => 'Нет данных',
                    'itemOptions' => [
                        'tag' => false,
                    ],
                    'options' => [
                        'class' => 'delimiter-list flex-row row'
                    ]
                ]);?>
            </div>
        <?php endif; ?>
    </div>
    <div class="col-md-4 hidden-sm hidden-xs">
        <div class="sticky">
            <div class="sidebar white-block scrollbar">
                <div class="contacts-list">
                    <p class="h3 bordered">
                        Контакты
                    </p>
                    <?php if($model->address): ?>
                        <div class="contacts-list--item">
                            <p class="title">
                                Адрес
                            </p>
                            <div class="content">
                                <p class="address">
                                    <?= $model->address ?>
                                </p>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if($contactDataProvider->totalCount > 0): ?>
                        <?= ListView::widget([
                            'dataProvider' => $contactDataProvider,
                            'itemView' => '/common/_contact-item',
                            'summary' => '',
                            'itemOptions' => [
                                'tag' => false
                            ],
                            'options' => [
                                'tag' => false,
                            ]
                        ]); ?>
                    <?php endif; ?>
                    <?php if(!is_null($model->offices) && count($model->offices) > 0): ?>
                        <div class="contacts-list--item">
                            <p class="title">
                                Офисы застройщика
                            </p>
                            <div class="content">
                                <?= $this->render('/common/_offices', [
                                    'offices' => $model->offices
                                ])?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
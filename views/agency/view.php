<?php
/* @var $model app\models\Agency */
/* @var $this yii\web\View */

use app\components\widgets\Placemark;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ListView;
use yii\widgets\Pjax;

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Агентства недвижимости', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$format = \Yii::$app->formatter;
?>


<div class="row flex-row agency-card">
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
                        <?= Html::img([Yii::getAlias("@web/img/office.png")]) ?>
                    <?php endif ?>
                </div>
            </div>
            <?php if($model->detail): ?>
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
            'latitude' => $model->latitude,
        ]) ?>
        
        <?php if($agentDataProvider->totalCount > 0): ?>
        <div class="white-block">
            <p class="h3 bordered">
                Агенты
            </p>
            <div class="person-list row">
                <?= ListView::widget([
                    'dataProvider' => $agentDataProvider,
                    'summary' => '',
                    'itemView' => '/common/_person-item',
                    'itemOptions' => [
                        'class' => 'col-xs-6'
                    ],
                    'viewParams' => [
                        'itemClass' => 'person-list--item'
                    ]
                ]);?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <div class="col-md-4  hidden-xs hidden-sm">
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
                    
                    <?php if($managerDataProvider->totalCount > 0): ?>
                    <div class="contacts-list--item">
                        <p class="title">
                            Администраторы агентства
                        </p>
                        <div class="content">
                            <?= ListView::widget([
                                'dataProvider' => $managerDataProvider,
                                'summary' => '',
                                'itemView' => '/common/_person-item',
                                'itemOptions' => [
                                    'tag' => false
                                ],
                                'viewParams' => [
                                    'itemClass' => 'person-item'
                                ]
                            ]);?>
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
                </div>
            </div>
        </div>
    </div>
</div>
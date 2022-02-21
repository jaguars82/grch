<?php
/* @var $model app\models\Bank */
/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ListView;
use app\components\widgets\Placemark;

$this->title = "Банк \"$model->name\"";
$this->params['breadcrumbs'][] = ['label' => 'Банки', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->name;
\yii\web\YiiAsset::register($this);
?>


<div class="row flex-row">
    <div class="col-xs-12 col-md-8">
        <div class="white-block description">
            <h2 class="bordered">
                <?= Html::encode($this->title) ?>
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
                        <?= Html::img([Yii::getAlias("@web/img/bank.png")]) ?>
                    <?php endif ?>
                </div>
            </div>
            
            <p class="h3 bordered">
                Тарифы
            </p>
            <div class="responsive-table tariff-table">
                <table >
                    <?= ListView::widget([
                        'dataProvider' => $tariffDataProvider,
                        'itemView' => '/common/_item-tariff',
                        'summary' => '',
                        'itemOptions' => [
                            'tag' => false,
                        ],
                        'options' => [
                            'tag' => false,
                        ],

                        'beforeItem' => function ($model, $key, $index, $widget) {
                            if(!$index) {
                                return '<tr>
                                    <td>Название</td>
                                    <td>Процентная ставка годовых</td>
                                    <td>Первоначальный взнос</td>
                                    <td>Срок ипотеки</td>
                                </tr>';
                            }
                        },
                        'emptyText' => '<div class="col-md-12">Тарифы еще не добавлены</div>'
                    ]);?>
                </table>
            </div>
        </div>
        <?= Placemark::widget([
            'address' => $model->address,
            'longitude' => $model->longitude,
            'latitude' => $model->latitude,
        ]) ?>
    </div>
    <div class="col-md-4 hidden-xs hidden-sm">
        <div class="sticky">
            <div class="sidebar white-block scrollbar">
                <div class="contacts-list">
                    <p class="h3 bordered">
                        Контакты
                    </p> 
                    <div class="contacts-list--item">
                        <p class="title">
                            Адрес
                        </p>
                        <div class="content">
                            <p class="address">
                                <?= !is_null($model->address) ? $model->address : 'нет данных' ?>
                            </p>
                        </div>
                    </div>
                    <!-- <div class="contacts-list--item">
                        <p class="title">
                            Администраторы агентства
                        </p>
                        <div class="content">
                            <div class="person-item flex-row">
                                <div class="info">
                                    <p class="name">
                                        Силин Борис Фёдорович
                                    </p>
                                    <a href="tel:(495) 146-1072" class="phone">(495) 146-1072</a>
                                    <a href="mailto:mail@city-vrn.ru" class="email">mail@city-vrn.ru</a>
                                </div>
                                <div class="image">
                                    <img src="img/blank-person.svg">
                                </div>
                            </div>
                        </div>
                    </div>  -->
                </div>
            </div>
        </div>
    </div>
</div>
<?php
/* @var $content string */
/* @var $this \yii\web\View */

use app\assets\NewAsset;
use app\models\Developer;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use yii\bootstrap\Nav;
use app\components\CityLocation;

$selectedCity = CityLocation::get();

NewAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>

<body>
    <?php $this->beginBody() ?>
    <div class="app">
        <?= $this->context->renderPartial('/common/_header.php') ?>
        <main class="admin-layout">
            <div class="container">
                <?= $this->context->renderPartial('/common/_alert.php') ?>

                <?= Breadcrumbs::widget([
                    'options' => [
                        'class' => 'breadcrumbs'
                    ],
                    'tag' => 'div',
                    'itemTemplate' => '{link}',
                    'activeItemTemplate' => '<span>{link}</span>',
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]) ?>

                <div class="row admin-layout-row">
                    <div class="col-sm-3 col-md-2 pb-20 sticky">
                        <?php
                        echo Nav::widget([
                            'options' => ['class' => 'nav nav-pills nav-stacked padding-bottom-sm'],
                            'items' => [
                                ['label' => 'Застройщики', 'url' => ['admin/developer/index']],
                                ['label' => 'ЖК', 'url' => ['admin/newbuilding-complex/index']],
                                ['label' => 'Агентства', 'url' => ['admin/agency/index']],
                                ['label' => 'Банки', 'url' => ['admin/bank/index']],
                                ['label' => 'Новости', 'url' => ['admin/news/index']],
                                ['label' => 'Справка', 'url' => ['admin/contact/contact/interaction']],
                                [
                                    'label' => 'Справочник',
                                    'url' => '#',
                                    'items' => [
                                        [
                                            'label' => 'Регионы',
                                            'url' => '/admin/region/index',
                                        ],
                                        [
                                            'label' => 'Города',
                                            'url' => '/admin/city/index',
                                        ],
                                        [
                                            'label' => 'Районы',
                                            'url' => '/admin/district/index',
                                        ],
                                        [
                                            'label' => 'Тип улицы',
                                            'url' => '/admin/street-type/index',
                                        ],
                                        [
                                            'label' => 'Тип здания',
                                            'url' => '/admin/building-type/index',
                                        ],
                                        [
                                            'label' => 'Преимущества',
                                            'url' => '/admin/advantage/index',
                                        ]
                                    ]
                                ],
                                ['label' => 'Помощь', 'url' => ['admin/faq/index']]
                            ],
                        ]);
                        ?>
                    </div>
                    <div class="col-sm-9 col-md-10">
                        <?= $content ?>
                    </div>
                </div>
            </div>
        </main>
        <?= $this->context->renderPartial('/common/_footer.php', [
            'developers' => Developer::find()->all(),
            'districts' => !is_null($selectedCity) ? $selectedCity->districts : null
        ]) ?>
    </div>
    <div class="overlay"></div>
    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>
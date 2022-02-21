<?php
/* @var $content string */
/* @var $this \yii\web\View */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

AppAsset::register($this);
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

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => [
            //['label' => 'Главная', 'url' => ['/']],
            [
                'label' => 'Главная', 
                'url' => [
                    //'/' . (($session->has('site-index-query-string')) ? '?' . $session->get('site-index-query-string') : '')
                    /*'/' . ((\Yii::$app->session->has('site-index-query-string-' . \Yii::$app->user->id)) 
                        ? '?' . \Yii::$app->session->get('site-index-query-string-' . \Yii::$app->user->id) 
                        : '')*/
                    '/' . ((\Yii::$app->request->cookies->has('site-index-query-string-' . \Yii::$app->user->id)) ? '?' . \Yii::$app->request->cookies->get('site-index-query-string-' . \Yii::$app->user->id) : '')
                ],
                'linkOptions' => ['class' => 'main-page-link'],
            ],
            
            ['label' => 'Новости', 'url' => ['/news']],
            
            //['label' => 'Поиск', 'url' => ['/site/search']],
            [
                'label' => 'Поиск',
                'url' => [
                    '/site/search' . ((\Yii::$app->request->cookies->has('site-search-query-string-' . \Yii::$app->user->id)) ? '?' . \Yii::$app->request->cookies->get('site-search-query-string-' . \Yii::$app->user->id) : '')
                ],
                'linkOptions' => ['class' => 'search-page-link'],
            ],
            
            [
                'label' => 'Карта', 
                'url' => [
                    '/site/map'  . ((\Yii::$app->request->cookies->has('map-search-query-string-' . \Yii::$app->user->id)) ? '?' . \Yii::$app->request->cookies->get('map-search-query-string-' . \Yii::$app->user->id) : '')
                ],
                'linkOptions' => ['class' => 'map-page-link'],
            ],
            
            ['label' => 'ЖК', 'url' => ['/newbuilding-complex']],
            ['label' => 'Застройщики', 'url' => ['/developer']],
            ['label' => 'Агентства', 'url' => ['/agency']],
            ['label' => 'Контакты', 'url' => ['/contact/contact']],
            ['label' => 'Взаимодействия', 'url' => ['/contact/contact/interaction']],
            ['label' => 'КП', 'url' => ['/offer/index'], 'visible' => Yii::$app->user->can('admin') || Yii::$app->user->can('agent')],
            ['label' => 'Избранное', 'url' => ['/favorite'], 'visible' => Yii::$app->user->can('admin') || Yii::$app->user->can('agent')],
            ['label' => 'Банки', 'url' => ['/bank'], 'visible' => Yii::$app->user->can('admin')],
            ['label' => 'Помощь', 'url' => ['/faq']],
            ['label' => 'Админ', 'url' => ['/admin'], 'visible' => Yii::$app->user->can('admin')],
            /*Yii::$app->user->isGuest ? (
                ['label' => 'Войти', 'url' => ['/auth/login']]
            ) : (
                ['label' => 'Выйти', 'url' => ['/auth/logout']]
            )*/
            ['label' => 'Выйти', 'url' => ['/auth/logout'], 'visible' => !Yii::$app->user->isGuest]
        ],
    ]);
    NavBar::end();
    ?>
    
    <div class="container">
         <div class="container" style="position: absolute; padding: 0; margin-left: -15px; ">
             <div class="alert alert-template" role="alert" style="display: none; margin: 15px; margin-top: 0">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <div class="alert-content"></div>
             </div>
             <div class="alert-seat">
                <div id="alert-block" style="margin-left: 15px; margin-right: 15px">
                    <?= Alert::widget() ?>
                </div>
             </div>
        </div>
        
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        
        <!--?= Alert::widget() ?-->
        
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; Арктическая лаборатория <?= date('Y') ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

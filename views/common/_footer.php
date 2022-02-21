<?php
use yii\helpers\Url;
use yii\helpers\Html;

$format = \Yii::$app->formatter;
?>
<footer class="footer">
    <div class="container">
        <div class="flex-row">
            <div class="col-md-2">
                <?= Html::img(\Yii::getAlias('@web/img/icons/logo-footer.svg'), ['class' => 'footer--logo'])?>
            </div>
            <div class="col-md-2 flats footer--menu">
                <p class="title">
                    Квартиры
                </p>
                <ul>
                    <li>
                        <?= Html::a('1- комнатные', ['site/search', 'AdvancedFlatSearch[roomsCount]' => [1]]) ?>
                    </li>
                    <li>
                        <?= Html::a('2- комнатные', ['site/search', 'AdvancedFlatSearch[roomsCount]' => [2]]) ?>
                    </li>
                    <li>
                        <?= Html::a('3- комнатные', ['site/search', 'AdvancedFlatSearch[roomsCount]' => [3]]) ?>
                    </li>
                    <li>
                        <?= Html::a('4- комнатные', ['site/search', 'AdvancedFlatSearch[roomsCount]' => [4]]) ?>
                    </li>
                </ul>
            </div>
            
            <?php if(!is_null($districts) && count($districts) > 0): ?>
                <div class="col-md-2 districts footer--menu">
                    <p class="title">
                        Районы
                    </p>
                    <ul>
                        <?php foreach($districts as $district): ?>
                            <li>
                                <?= Html::a($format->asCapitalize($district->name), ['site/search', 'AdvancedFlatSearch[district]' => $district->id]); ?>
                            </li>
                        <?php endforeach;?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="col-md-2 developers footer--menu">
                <p class="title">
                    Застройщики
                </p>
                <ul>
                    <?php foreach($developers as $developer): ?>
                    <li>
                        <?= Html::a($format->asCapitalize($developer->name), ['developer/view', 'id' => $developer->id]); ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="col-md-4">
                <ul class="footer--menu__right">
                    <li><a href="/news">Новости</a></li>
                    <li><a href="/bank">Банки</a></li>
                    <li><a href="/agency">Агентства</a></li>
                    <li><a href="/faq">Поддержка</a></li>
                    <li><a href="/newbuilding-complex">ЖК</a></li>
                </ul>
            </div>  
        </div> 
        <p class="footer--copyright">
            Гильдия Риэлторов Черноземья <?= date('Y'); ?>
        </p>
    </div>
</footer>
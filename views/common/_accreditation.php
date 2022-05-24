<?php
use app\assets\viewElements\AccreditationAsset;
use yii\helpers\Html;
use yii\helpers\Url;

AccreditationAsset::register($this);

$format = \Yii::$app->formatter;
?>

<?php if(count($banks)): ?>
    <div class="bank-list-container">
        <?php foreach($banks as $bank): ?>

            <div id="bank-badge-<?=$bank->id?>" class="bank-logo-container">
                <?php if(!is_null($bank->logo)): ?>
                    <?= Html::img(Yii::getAlias("@web/uploads/{$bank->logo}")) ?>
                <?php else: ?>
                    <?= Html::img(Yii::getAlias("@web/img/bank.png")) ?>
                <?php endif ?>
            </div>

            <template id="bank-badge-<?=$bank->id?>-menu" type="text/x-kendo-template">
                <div class="text-center bank-title-container"><span><strong><?= $bank->name ?></strong></span></div>
                <ul class="profile-menu-list">
                    <?php if (!empty($bank->url)): ?>
                    <li class="profile-menu-item">
                        <a target="_blank" href="<?=$bank->url?>">
                            <span class="material-icons-outlined">public</span><span class="item-text">Сайт банка</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if(isset($isEnableCalculation) && $isEnableCalculation && isset($flat->newbuildingComplex->bank_tariffs[$bank->id])): ?>
                        <li class="profile-menu-item">
                            <?= Html::a('<span class="material-icons-outlined">calculate</span><span class="item-text">Рассчитать кредит</span>', ['bank/calculation', 'id' => $bank->id, 'flatId' => $flat->id]) ?>
                        </li>
                    <?php endif; ?>
                </ul>
            </template>


        <!--<div class="<?= $colSizeClass ?>">
            <div class="bank-inline-list--item">
                <p class="title">
                    <?= Html::a($bank->name, ['bank/view', 'id' => $bank->id]) ?>
                </p>
                <div class="image">
                    <a href="<?= Url::to(['bank/view', 'id' => $bank->id]) ?>">
                        <?php if(!is_null($bank->logo)): ?>
                            <?= Html::img([Yii::getAlias("@web/uploads/{$bank->logo}")]) ?>
                        <?php else: ?>
                            <?= Html::img([Yii::getAlias("@web/img/bank.png")]) ?>
                        <?php endif ?>
                    </a>
                </div>
                
                <?php if(isset($isEnableCalculation) && $isEnableCalculation && isset($flat->newbuildingComplex->bank_tariffs[$bank->id])): ?>
                    <?= Html::a('Рассчитать кредит', ['bank/calculation', 'id' => $bank->id, 'flatId' => $flat->id], ['class' => 'btn btn-white']) ?>
                <?php endif; ?>

                <?php if(!is_null($newbuildingComplex) && !is_null($newbuildingComplex->minYearlyRate)): ?>
                    <div class="btn btn-red">
                        Ставка от <?= $format->asPercent($newbuildingComplex->minYearlyRate) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>-->
        <?php endforeach; ?>
    </div>  
<?php else: ?>
    <p><?= $noDataMessage ?></p>
<?php endif ?>
<?php
use yii\helpers\Html;
use yii\helpers\Url;

$format = \Yii::$app->formatter;
?>

<?php if(count($banks)): ?>
    <div class="bank-inline-list delimiter-list row">
        <?php foreach($banks as $bank): ?>
        <div class="<?= $colSizeClass ?>">
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
        </div>
        <?php endforeach; ?>
    </div>  
<?php else: ?>
    <p><?= $noDataMessage ?></p>
<?php endif ?>
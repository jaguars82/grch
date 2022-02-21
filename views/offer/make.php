<?php
use app\assets\OfferMakeAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$format = \Yii::$app->formatter;

$this->title = 'КП - ' . $flat->getAddress();
$this->params['breadcrumbs'][] = ['label' => 'КП', 'url' => ['offer/index']];
$this->params['breadcrumbs'][] = isset($offer) ? $offer->id : 'Сформировать';

$settings = isset($offer) ? json_decode($offer->settings, true) : [];

OfferMakeAsset::register($this);
?>

<div class="row">
        
    <div class="col-md-8">
        <?php if(!Yii::$app->user->isGuest && $flat->newbuildingComplex->offer_new_price_permit): ?>
            <div class="alert alert-info" role="alert" style="margin-bottom: 20px">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                Вы можете установить произвольную стоимость предложения
            </div>
        <?php endif ?>
        <div class="commercial-offer-layout">
            <?php if(isset($offer)): ?>
                <?= $this->render('_common', [
                    'flat' => $flat, 
                    'settings' => $settings,
                    'offer' => $offer,
                    'user' => \Yii::$app->user->identity,
                    'isPlacemarkImage' => true,
                ]) ?>
            <?php else: ?>
                <?= $this->render('_common', [
                    'flat' => $flat, 
                    'settings' => $settings,
                    'user' => \Yii::$app->user->identity,
                    'isPlacemarkImage' => true,
                ]) ?>
            <?php endif ?>
        </div>
    </div>

    <div class="col-md-4 format-block">
        <div class="panel panel-default">
            <div class="panel-heading">
                Настройки форматирования
            </div>
            <div class="panel-body">
                <?php $form = ActiveForm::begin(['id' => 'offer-form']); ?>
                
                <div style="display: none">
                    <input type="text" name="new_price_cash" value="<?= isset($offer) ? $offer->new_price_cash : $flat->cashPriceWithDiscount ?>">
                    <input type="text" name="new_price_credit" value="<?= isset($offer) ? $offer->new_price_credit : (is_null($flat->price_credit) ? '': $flat->creditPriceWithDiscount) ?>">
                </div>
                
                <?php if(!is_null(Yii::$app->user->identity->agency)): ?>
                    <label style="font-weight: normal" data-toggle="collapse" data-target="#agency-info">
                        <input type="checkbox" name="settings[agency]" <?php if(isset($settings['agency'])): ?>checked="checked"<?php endif ?>><span style="margin-left: 5px;">Информация о компании</span>
                    </label><br>
                <?php endif ?>
                    
                <label style="font-weight: normal" data-toggle="collapse" data-target="#user-info">
                    <input type="checkbox" name="settings[user]" <?php if(isset($settings['user'])): ?>checked="checked"<?php endif ?>><span style="margin-left: 5px;">Информация о сотруднике</span>
                </label><br>
                <label style="font-weight: normal" data-toggle="collapse" data-target="#developer-info">
                    <input type="checkbox" name="settings[developer]" <?php if(isset($settings['developer'])): ?>checked="checked"<?php endif ?>><span style="margin-left: 5px;">Информация о застройщике</span>
                </label><br>
                <label style="font-weight: normal" data-toggle="collapse" data-target="#newbuilding-complex-info">
                    <input type="checkbox" name="settings[newbuilding_complex]" <?php if(isset($settings['newbuilding_complex'])): ?>checked="checked"<?php endif ?>><span style="margin-left: 5px;">Информация о ЖК</span>
                </label><br>
                <label style="font-weight: normal" data-toggle="collapse" data-target="#furnishes-info">
                    <input type="checkbox" name="settings[furnishes]" <?php if(isset($settings['furnishes'])): ?>checked="checked"<?php endif ?>><span style="margin-left: 5px;">Информация по отделке</span>
                </label><br>
                
                <?php ActiveForm::end(); ?>
            </div>  
        </div>
        
        <div>
            <ul style="margin-left: 0; padding-left: 0; list-style: none;">
                <?php if(isset($offer)): ?>
                    <li style="margin-bottom: 10px">
                        <?= Html::a('Сохранить', 'javascript:void(0);',
                            [
                                'class' => 'update-offer-btn btn btn-primary',
                                'data' => ['target' => Url::to(['update', 'id' => $offer->id])]
                            ]
                        ) ?>
                    </li>
                    <li style="margin-bottom: 10px">
                        <?= Html::a('Отмена', ['/offer/index'], ['class' => 'btn btn-danger']) ?>
                    </li>
                <?php endif ?>
                
                <li style="margin-bottom: 10px">
                    <?= Html::a('Отправить на почту', 'javascript:void(0);',
                        [
                            'class' => 'send-email-btn btn btn-primary',
                            'data' => ['target' => Url::to(['send-email', 'flatId' => $flat->id])]
                        ]
                    ) ?>
                </li>
                
                <li style="margin-bottom: 10px">
                    <?= Html::a('Отправить на Telegram', 'javascript:void(0);',
                        [
                            'class' => 'telegram-btn btn btn-primary',
                            'data' => ['target' => Url::to(['telegram', 'flatId' => $flat->id])]
                        ]
                    ) ?>
                </li>
                
                <li style="margin-bottom: 10px"><?= Html::a('Распечатать', '#', ['class' => 'btn btn-primary', 'onclick' => "window.print();"]) ?></li>
                
                <li style="margin-bottom: 10px">
                    <?= Html::a('Сделать короткую ссылку', 'javascript:void(0);',
                        [
                            'class' => 'link-btn btn btn-primary',
                            'data' => ['target' => Url::to(['make', 'flatId' => $flat->id])]
                        ]
                    ) ?>
                </li>
                
                <li style="margin-bottom: 10px"><?= Html::a('Скачать PDF', ['download-pdf', 'flatId' => $flat->id], ['class' => 'btn btn-primary download-pdf-btn', 'target' => '_blank', 'data-target' => Url::to(['download-pdf', 'flatId' => $flat->id])]) ?></li>
            </ul>
        </div>
    </div>

</div>

<div class="modal fade" id="link-view" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="margin: 0 auto;">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title">Короткая ссылка</h3>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top:-45px;margin-right:-12px;">
            <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <span id="link-value1" style="display: inline-block; height:50px; line-height: 50px">
              <?= Html::a('', '', ['id' => 'link-value', 'target' => '_blank']) ?>
          </span>
      </div>
    </div>
  </div>
</div>
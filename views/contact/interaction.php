<?php
use app\assets\InteractionsAsset;

$this->title = 'Справка';
$this->params['breadcrumbs'][] = $this->title;

$format = \Yii::$app->formatter;
InteractionsAsset::register($this);
?>

<div class="interaction white-block">
    <h1><?= $this->title ?></h1>
    
    <p>Здесь вы можете получить информацию по взаимодействию на объекте</p>
    
    <div class="row" style="margin-bottom: 15px; margin-top: 30px">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label" >Застройщик</label><br>
                <div class="inline-select form-group">
                    <select data-placeholder="Застройщик" id="developer-select" class="form-control" style="width: 100%">
                        <?php foreach($developers as $developer): ?>
                            <option value="<?= $developer->id ?>"><?= $developer->name ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>
            
            <div class="developer-contact-template" style="margin-bottom: 15px; display: none">
                <?= $this->render('_item-contact') ?>
            </div>
            
            <div id="developer-info" style="padding-top: 20px"></div>
        </div>
        
        <div class="col-md-6">
            <label class="control-label" >Жилой комплекс</label><br>
            
            <div class="inline-select form-group">
                <select data-placeholder="Жилой комплекс" id="newbuilding-complex-select" class="form-control" style="width: 100%"></select>
            </div>
            
            <div id="newbuilding-complex-algorithm-template" style="margin-bottom: 30px; display: none">
                <h3 style="margin-top: 0; margin-bottom: 5px" class="contact-type">Регламент показа</h3>
                <div style="margin-top: 5px" class="algorithm-text"></div>
            </div>
            
            <div class="newbuilding-complex-contact-template" style="margin-bottom: 15px; display: none">
                <?= $this->render('_item-contact') ?>
            </div>
            
            <div id="newbuilding-complex-info" style="padding-top: 20px"></div>
        </div>
    </div>
</div>
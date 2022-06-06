<?php
use app\assets\widgets\FlatsChessAsset;
use app\components\widgets\FlatsChess;
use app\models\Flat;
use yii\helpers\Url;
use yii\helpers\Html;

FlatsChessAsset::register($this);

$format = \Yii::$app->formatter;
?>

<?php if (count($newbuildings)): ?>

    <div class="white-block flat-chess">
        <p class="h3">Шахматки / Позиции</p>
        <?php foreach($newbuildings as $key => $newbuilding): ?>
            <?php if($newbuilding->active != 0): ?>
            <div class="flat-chess__item 
                <?php if(!is_null($currentFlat) && $currentFlat->newbuilding->id == $newbuilding->id): ?>
                    flat-chess marked open
                <?php endif; ?>">
                <div class="info flex-row">
                    <span class="info--item position">
                        <?= $format->asCapitalize($newbuilding->name) ?>
                    </span>
                    <span class="info--item available-flats">
                        <?php if ($newbuilding->getActiveFlats()->count() > 0): ?>
                        <?= \MessageFormatter::formatMessage(
                            'be',
                            // ' {flats, plural, one{Доступна # квартира} few{Доступно # квартиры} other{Доступно # квартир}}',
                            ' {flats, plural, one{Доступна #} few{Доступно #} other{Доступно #}}',
                            ['flats' => $newbuilding->getActiveFlats()->count()]
                        );
                        ?>
                        <?php endif; ?>
                        <?php if ($newbuilding->getReservedFlats()->count() > 0): ?>
                        <?= \MessageFormatter::formatMessage(
                            'be',
                            // ' {flats, plural, one{Зарезервирована # квартира} few{Зарезервировано # квартиры} other{Зарезервировано # квартир}}',
                            ' {flats, plural, one{Забронирована #} few{Забронировано #} other{Забронировано #}}',
                            ['flats' => $newbuilding->getReservedFlats()->count()]
                        );
                        ?>
                        <?php endif; ?>
                    </span>
                    <span class="info--item deadline">
                        Сдача <?= is_null($newbuilding->deadline) ? 'Нет данных' : $format->asQuarterAndYearDate($newbuilding->deadline) ?>
                    </span>
                    <span class="info--item floors"> <?= $newbuilding->total_floor ?> этажей </span>
                </div>
                <div class="content">
                    <div class="abbreviation flex-row">
                        <?php foreach(Flat::$status as $key => $status): ?>
                            <span class="<?= FlatsChess::STATUS_CLASS[$key] ?>"><?= $status ?></span>
                        <?php endforeach ?>
                        <span class="<?= FlatsChess::NO_FLAT_CLASS ?>">Недоступно</span>
                    </div>
                    <?php if (isset($maxRoomsOnFloors[$newbuilding->id]) && isset($sectionsFlats[$newbuilding->id]) && isset($sectionsData[$newbuilding->id])): ?>
                        <?= $this->render('/widgets/_newbuilding-flats-chess', [
                            'newbuilding' => $newbuilding,
                            'maxRoomsOnFloors' => $maxRoomsOnFloors[$newbuilding->id],
                            'sectionsFlats' => $sectionsFlats[$newbuilding->id],
                            'sectionsData' => $sectionsData[$newbuilding->id],
                            'currentFlat' => $currentFlat
                        ]) ?>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
<?php endif ?>
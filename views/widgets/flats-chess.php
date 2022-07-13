<?php
use app\assets\widgets\FlatsChessAsset;
use app\components\widgets\FlatsChess;
use app\models\Flat;
use yii\helpers\Url;
use yii\helpers\Html;

FlatsChessAsset::register($this);

$format = \Yii::$app->formatter;
?>

<?php if (isset($use_virtual_structure) && $use_virtual_structure == 1): ?>

    <?php if (count($newbuildings)): ?>
        <div class="white-block flat-chess">
            <p class="h3">Шахматки / Позиции</p>
            <?php foreach($newbuildings as $key => $newbuilding): ?>
                <div class="flat-chess__item
                <?php if(!is_null($currentFlat) && in_array($currentFlat->entrance_id, $newbuilding->entrance_idies)): ?>
                    flat-chess marked open
                <?php endif; ?>">
                    <div class="info flex-row">
                        <span class="info--item position">
                            <?= $format->asCapitalize($newbuilding->name) ?>
                        </span>

                        <span class="info--item available-flats">
                            <?php if ($newbuilding->active_flats > 0): ?>
                            <?= \MessageFormatter::formatMessage(
                                'be',
                                ' {flats, plural, one{Доступна #} few{Доступно #} other{Доступно #}}',
                                ['flats' => $newbuilding->active_flats]
                            );
                            ?>
                            <?php endif; ?>
                            <?php if ($newbuilding->reserved_flats > 0): ?>
                            <?= \MessageFormatter::formatMessage(
                                'be',
                                ' {flats, plural, one{Бронь #} few{Бронь #} other{Бронь #}}',
                                ['flats' => $newbuilding->reserved_flats]
                            );
                            ?>
                            <?php endif; ?>
                        </span>
                        <span class="info--item deadline">
                            Сдача: <?= (empty($newbuilding->deadline) ? 'Нет данных' : strtotime(date("Y-m-d")) > strtotime($newbuilding->deadline)) ? 'позиция сдана' : $format->asQuarterAndYearDate($newbuilding->deadline) ?>
                        </span>
                        <span class="info--item floors"> 
                            <?= empty($newbuilding->total_floor) ? 'этажность не указана' : $newbuilding->total_floor.' этажей' ?>
                        </span>
                    </div>

                    <div class="content">
                        <div class="abbreviation flex-row">
                            <?php foreach(Flat::$status as $key => $status): ?>
                                <span class="<?= FlatsChess::STATUS_CLASS[$key] ?>"><?= $status ?></span>
                            <?php endforeach ?>
                            <span class="<?= FlatsChess::NO_FLAT_CLASS ?>">Недоступно</span>
                        </div>
                        <?php if (isset($maxRoomsOnFloors[$newbuilding->id]) && isset($sectionsFlats[$newbuilding->id])/* && isset($sectionsData[$newbuilding->id])*/): ?>
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
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
<?php else: ?>
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
                                ' {flats, plural, one{Бронь #} few{Бронь #} other{Бронь #}}',
                                ['flats' => $newbuilding->getReservedFlats()->count()]
                            );
                            ?>
                            <?php endif; ?>
                        </span>
                        <span class="info--item deadline">
                            Сдача: <?= (is_null($newbuilding->deadline) ? 'Нет данных' : strtotime(date("Y-m-d")) > strtotime($newbuilding->deadline)) ? 'позиция сдана' : $format->asQuarterAndYearDate($newbuilding->deadline) ?>
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
    <?php endif; ?>
<?php endif; ?>

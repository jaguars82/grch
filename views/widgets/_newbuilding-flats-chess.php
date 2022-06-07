<?php

$format = \Yii::$app->formatter;

?>

<div class="sections-container">
<?php foreach($sectionsData as $section): ?>
    <?php
        $entrancesData = $sectionsFlats['entrances_data'];
        $maxRoomsOnFloor = $maxRoomsOnFloors[$section];
        $flats = [];
        $sectionFlats = $sectionsFlats[$section];
        $lastSectionFlat = count($sectionFlats) - 1;
        $lastFloor = $newbuilding->total_floor + 1;
    ?>
    
    <?php if (!empty($entrancesData[$section]['id'])): ?>
        <div id="entrance-<?= $entrancesData[$section]['id'] ?>" class="section <?php if (!is_null($currentFlat) && $currentFlat->entrance->id == $entrancesData[$section]['id']): ?>active<?php endif; ?>" onclick="toggleEntrance(<?= $entrancesData[$section]['id'] ?>)">
            <div class="section-arrow material-icons-outlined">chevron_right</div>
            <div>
                <strong><?= $entrancesData[$section]['name'] ?></strong>
            </div>

            <?php if ($entrancesData[$section]['activeFlats'] > 0): ?>
                <?= \MessageFormatter::formatMessage(
                    'be',
                    // ' {flats, plural, one{, доступна # квартира} few{, доступно # квартиры} other{, доступно # квартир}}',
                    ' {flats, plural, one{, доступна #} few{, доступно #} other{, доступно #}}',
                    ['flats' => $entrancesData[$section]['activeFlats']]
                );
                ?>
            <?php endif; ?>
            <?php if ($entrancesData[$section]['reservedFlats'] > 0): ?>
                <?= \MessageFormatter::formatMessage(
                    'be',
                    // ' {flats, plural, one{, зарезервирована # квартира} few{, зарезервировано # квартиры} other{, зарезервировано # квартир}}',
                    ' {flats, plural, one{, забронирована - #} few{, забронировано - #} other{, забронировано - #}}',
                    ['flats' => $entrancesData[$section]['reservedFlats']]
                );
                ?>
            <?php endif; ?>
            <?php if (!empty($entrancesData[$section]['deadline'])): ?>
                <div>
                , cдача - <?= is_null($entrancesData[$section]['deadline']) ? 'нет данных' : $format->asQuarterAndYearDate($entrancesData[$section]['deadline']) ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($entrancesData[$section]['floors'])): ?>
                <div>
                , <?= $entrancesData[$section]['floors'] ?> этажей
                </div>
            <?php endif; ?>
            <?php if (!empty($entrancesData[$section]['material'])): ?>
                <div>
                , <?= $entrancesData[$section]['material'] ?>
                </div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <p class="section">Подъезд №<?= $section ?></p>
    <?php endif; ?>

    <div id="chess-entrance-<?= $entrancesData[$section]['id']?>" class="chess-table" <?php if (!empty($entrancesData[$section]['id']) && $currentFlat->entrance_id != $entrancesData[$section]['id']): ?>style="display: none;"<?php endif; ?>>
        <div class="responsive-table">
            <table>
                <?php foreach ($sectionFlats as $key => $flat) {
                    if ((count($flats) && $flat->floor != $lastFloor) || $key == $lastSectionFlat && count($flats) > 1) {
                        $flatsArray = $flats;
                        $flats = [];
                        if ($key == $lastSectionFlat && $flat->floor == $lastFloor) {
                            $flatsArray[] = $flat;
                        }

                        echo $this->render('/widgets/_flat-chess-floor', ['flatsArray' => $flatsArray, 'maxRoomsOnFloor' => $maxRoomsOnFloor, 'currentFlat' => $currentFlat]);
                    }
                    if ($key == $lastSectionFlat && $flat->floor != $lastFloor) {

                        echo $this->render('/widgets/_flat-chess-floor', ['flatsArray' => [$flat], 'maxRoomsOnFloor' => $maxRoomsOnFloor, 'currentFlat' => $currentFlat]);
                    }
                    $flats[] = $flat;
                    $lastFloor = $flat->floor;
                } 
                for ($i = $lastFloor - 1; $i > 0; $i--) {
                    echo $this->render('/widgets/_flat-chess-empty-floor', ['floor' => $i, 'maxRoomsOnFloor' => $maxRoomsOnFloor]);
                }
                ?>
            </table>
        </div>
    </div>    
<?php endforeach ?>
</div>
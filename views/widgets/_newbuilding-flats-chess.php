<?php foreach($sectionsData as $section): ?>
    <?php
        $entrancesData = $sectionsFlats['entrances_data'];
        $maxRoomsOnFloor = $maxRoomsOnFloors[$section];
        $flats = [];
        $sectionFlats = $sectionsFlats[$section];
        $lastSectionFlat = count($sectionFlats) - 1;
        $lastFloor = $newbuilding->total_floor + 1;
    ?>
    
    <?php if (!empty($entrancesData[$section]['id'])) : ?>
        <p class="section">
            <?= $entrancesData[$section]['name'] ?>
        </p>
    <?php else : ?>
        <p class="section">Подъезд №<?= $section ?></p>
    <?php endif; ?>

    <div class="chess-table">
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
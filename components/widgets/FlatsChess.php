<?php

namespace app\components\widgets;

use app\models\Flat;
use app\models\Newbuilding;
use yii\base\Widget;

/**
 * Widget for view flats in chess form
 */
class FlatsChess extends Widget
{
    const STATUS_CLASS = [
        Flat::STATUS_SALE => 'free',
        Flat::STATUS_SOLD => 'booked',
        Flat::STATUS_RESERVED => 'sales',
    ];
    
    const NO_FLAT_CLASS = 'not-available';
       
    public $newbuildings;
    public $currentFlat = null;
    
    /**
     * {@inheritdoc}
     */
    public function run()
    {   
        $sectionsFlats = [];
        $maxRoomsOnFloors = [];
        $sectionsData = [];
        
        if (count($this->newbuildings)) {
            $newbuildingIds = [];
            foreach ($this->newbuildings as $newbuilding) {
                $newbuildingIds[] = $newbuilding->id;
            }

            foreach (Flat::find()->where(['IN', 'newbuilding_id', $newbuildingIds])->orderBy(['floor' => SORT_DESC, 'number' => SORT_DESC])->all() as $flat) {
                $sectionsFlats[$flat->newbuilding_id][$flat->section][] = $flat;
            }

            foreach (Newbuilding::maxRoomsOnFloorsForNewbuildings($newbuildingIds) as $sectionData) {
                $maxRoomsOnFloors[$sectionData['newbuilding_id']][$sectionData['section']] = $sectionData['max_flats_on_floor'];
            }

            foreach (Newbuilding::getSectionsDataForNewbuildings($newbuildingIds) as $sectionData) {
                $sectionsData[$sectionData['newbuilding_id']][] = $sectionData['section'];
            }
        }
        
        return $this->render('/widgets/flats-chess', [
            'newbuildings' => $this->newbuildings,
            'sectionsFlats' => $sectionsFlats,
            'maxRoomsOnFloors' => $maxRoomsOnFloors,
            'sectionsData' => $sectionsData,
            'currentFlat' => $this->currentFlat
        ]);
    }
}

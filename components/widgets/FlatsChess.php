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
    public $use_virtual_structure;
    
    /**
     * {@inheritdoc}
     */
    public function run()
    {   
        $sectionsFlats = [];
        $maxRoomsOnFloors = [];
        $sectionsData = [];
        
        if ($this->use_virtual_structure == 1) {

        } else {

            if (count($this->newbuildings)) {
                $newbuildingIds = [];
                foreach ($this->newbuildings as $newbuilding) {
                    $newbuildingIds[] = $newbuilding->id;
                    $sectionsFlats[$newbuilding->id]['entrances_data'] = array();
                }
    
                foreach (Flat::find()->where(['IN', 'newbuilding_id', $newbuildingIds])->orderBy(['floor' => SORT_DESC, 'number' => SORT_DESC])->all() as $flat) {
    
                    // if there is a corresponding record in 'entrance' table - put information into array 'entrances_data'
                    if (!empty($flat->entrance_id)) {
                        $sectionsFlats[$flat->newbuilding_id][$flat->entrance->number][] = $flat;
                        if (!array_key_exists($flat->entrance->number, $sectionsFlats[$flat->newbuilding_id]['entrances_data'])) {
                            $sectionsFlats[$flat->newbuilding_id]['entrances_data'][$flat->entrance->number] =
                            [
                                'id' => $flat->entrance->id,
                                'name' => $flat->entrance->name,
                                'number' => $flat->entrance->number,
                                'floors' => $flat->entrance->floors,
                                'material' => $flat->entrance->material,
                                'status' => $flat->entrance->status,
                                'deadline' => $flat->entrance->deadline,
                                'azimuth' => $flat->entrance->azimuth,
                                'longitude' => $flat->entrance->longitude,
                                'latitude' => $flat->entrance->latitude,
                                'activeFlats' => $flat->entrance->getActiveFlats()->count(),
                                'reservedFlats' => $flat->entrance->getReservedFlats()->count(),
                            ];
                        }
                    } else {
                        $sectionsFlats[$flat->newbuilding_id][$flat->section][] = $flat;
                    }
                }
    
                foreach (Newbuilding::maxRoomsOnFloorsForNewbuildings($newbuildingIds) as $sectionData) {
                    $maxRoomsOnFloors[$sectionData['newbuilding_id']][$sectionData['section']] = $sectionData['max_flats_on_floor'];
                }
    
                foreach (Newbuilding::getSectionsDataForNewbuildings($newbuildingIds) as $sectionData) {
                    $sectionsData[$sectionData['newbuilding_id']][] = $sectionData['section'];
                }
            }
        }
        
        return $this->render('/widgets/flats-chess', [
            'use_virtual_structure' => $this->use_virtual_structure,
            'newbuildings' => $this->newbuildings,
            'sectionsFlats' => $sectionsFlats,
            'maxRoomsOnFloors' => $maxRoomsOnFloors,
            'sectionsData' => $sectionsData,
            'currentFlat' => $this->currentFlat
        ]);
    }
}

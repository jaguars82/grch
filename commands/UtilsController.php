<?php

namespace app\commands;

use yii\console\Controller;
use app\models\Newbuilding;
use app\models\Entrance;
use app\models\Flat;

class UtilsController extends Controller
{
    /**
     * Action to fill 'entrance_id' field in 'flat' table
     * according to the value of its 'section' field
     * It also adds a new record to 'entrance' table if needed
     */
    public function actionFetchEntrances() {

        $Newbuildings = (new Newbuilding())->find()->all();

        foreach($Newbuildings as $Newbuilding) {

            $NewbuildingEntrances = array();

            foreach($Newbuilding->flats as $flat) {
                
                // try to find entrance in database
                if(empty($flat->entrance_id) && !empty($flat->section)) {
                    $entrance = (new Entrance())
                    ->find()
                    ->where([ 'newbuilding_id' => $flat->newbuilding_id ])
                    ->andWhere([ 'number' => $flat->section ])
                    ->one();

                    if($entrance instanceof Entrance) { // if entrance exists: save its ID in 'entrance_id' field for current flat
                        $flat->entrance_id = $entrance->id;
                        $flat->save();
                    } else { // if entrance doesn't exist: add a new record to 'entrance' table and save its ID in 'entrance_id' field for current flat
                        $entrance = new Entrance();
                        $entrance->newbuilding_id = $Newbuilding->id;
                        $entrance->name = 'Подъезд '.$flat->section;
                        $entrance->number = $flat->section;
                        $entrance->floors = $Newbuilding->total_floor;
                        $entrance->material = $Newbuilding->material;
                        $entrance->save();
                        $flat->entrance_id = $entrance->id;
                        $flat->save();
                    }
                }
            }
        }
    } 
}
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
    public function actionFetchEntrances()
    {
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
                    } else { // if entrance doesn't exist: add a new record to 'entrance' table and copy some data from 'newbuilding' table to 'entrance' table
                        $entrance = new Entrance();
                        $entrance->newbuilding_id = $Newbuilding->id;
                        $entrance->name = 'Подъезд '.$flat->section;
                        $entrance->number = $flat->section;
                        $entrance->floors = $Newbuilding->total_floor;
                        $entrance->material = $Newbuilding->material;
                        $entrance->status = $Newbuilding->status;
                        $entrance->deadline = $Newbuilding->deadline;
                        $entrance->save();
                        // save entrance's ID in 'entrance_id' field for current flat
                        $flat->entrance_id = $entrance->id;
                        $flat->save();
                    }
                }
            }
        }
    }

    /**
     * Action to fill 'index_on_floor' field in 'flat' table
     * according to flat's position on the floor
     */
    public function actionFetchFlatIndexes($newbuildingId = 0)
    {
        if ($newbuildingId === 0) {
            $entrances = Entrance::find()->all();
        } else {
            $building = Newbuilding::findOne($newbuildingId);
            $entrances = $building->entrances;
        }

        foreach ($entrances as $entrance) {
            $flats = Flat::find()
                ->where(['entrance_id' => $entrance->id])
                ->all();

            $flatsGrouppedByFloor = array();

            foreach ($flats as $flat) {
                $flatsGrouppedByFloor[$flat->floor][] = $flat;
            }

            foreach ($flatsGrouppedByFloor as $floor => $floorFlats) {
                $flatIndex = 1;
                foreach ($floorFlats as $flat) {
                    $flat->index_on_floor = $flatIndex;
                    $flat->save();
                    $flatIndex++;
                }
            }
        }
    }

    /**
     * command to generate password hash from given password
     */
    public function actionMakePassHash ($pass) {
        $hash = \Yii::$app->security->generatePasswordHash($pass);
        echo $hash;
    }
}
<?php

namespace app\commands;

use yii\console\Controller;
use app\models\NewbuildingComplex;
use app\models\News;

class ServiceController extends Controller
{
    public function actionHideZeroPositions() {

        $NewbuildingComplexes = (new NewbuildingComplex())->find()->all();

        foreach($NewbuildingComplexes as $NewbuildingComplex) {
            $Newbuildings = $NewbuildingComplex->newbuildings;

            $newbuildingComplexBuildingsStatuses = array();

            // processing newbuildings to update 'active' field
            foreach($Newbuildings as $Newbuilding) {
                $active_flats = $Newbuilding->getActiveFlats()->count();
                $reserved_flats = $Newbuilding->getReservedFlats()->count();
                $aviable_flats = $active_flats + $reserved_flats;

                if($aviable_flats === 0) {
                    $Newbuilding->active = 0;
                    array_push($newbuildingComplexBuildingsStatuses, false);
                } else {
                    $Newbuilding->active = 1;
                    array_push($newbuildingComplexBuildingsStatuses, true);
                }
                $Newbuilding->save();
            }
            // updating 'has_active_buildings' in 'newbuilding_complex' table
            $NewbuildingComplex->has_active_buildings = in_array(true, $newbuildingComplexBuildingsStatuses, true) ? 1 : 0;
            $NewbuildingComplex->save();
        }        
    }

    public function actionDisableExpiredActions() {

        $actions = (new News())->actions;

        foreach ($actions as $action) {

            if (!empty($action->actionData->expired_at) && $action->actionData->expired_at != NULL) {

                if (strtotime(date("Y-m-d")) > strtotime($action->actionData->expired_at)) {
                    $flatsList = $action->assignedFlats;

                    foreach ($flatsList as $flat) {
                        $flat->discount = 0;
                        $flat->save();
                        $flat->unlink('assignedNews', $action, true);
                    }

					$action->is_archived = 1;
					$action->save();
                    $action->actionData->is_actual = 0;
                    $action->actionData->save();
                } 
            }
        }
    }
}
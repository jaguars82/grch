<?php

namespace app\commands;

use yii\console\Controller;
use app\models\Newbuilding;
use app\models\NewbuildingComplex;

class ServiceController extends Controller
{
    public function actionHideZeroPositions() {

        $NewbuildingComplexes = (new NewbuildingComplex())->find()->all();

        foreach($NewbuildingComplexes as $NewbuildingComplex) {
            $Newbuildings = $NewbuildingComplex->newbuildings;

            foreach($Newbuildings as $Newbuilding) {
                $active_flats = $Newbuilding->getActiveFlats()->count();
                $reserved_flats = $Newbuilding->getReservedFlats()->count();
                $aviable_flats = $active_flats + $reserved_flats;

                if($aviable_flats === 0) {
                    $Newbuilding->active = 0;
                } else {
                    $Newbuilding->active = 1;
                }
                $Newbuilding->save();
            }
        }        
    }
}
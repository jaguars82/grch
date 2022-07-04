<?php

namespace app\commands;

use yii\console\Controller;
use app\models\service\Developer;
use app\models\NewbuildingComplex;
use app\models\Newbuilding;
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

            if (!empty($action->actionData->expired_at) && $action->actionData->expired_at =! NULL) {

                if (strtotime(date("Y-m-d")) > strtotime($action->actionData->expired_at)) {
                    $flatsList = $action->assignedFlats;

                    foreach ($flatsList as $flat) {
                        $flat->discount = 0;
                        $flat->save();
                        $flat->unlink('assignedNews', $action, true);
                    }

                    $action->actionData->is_actual = 0;
                    $action->actionData->save();
                } 
            }
        }
    }


	public function actionFeedInquiry() {

        $id = 13;

        if (($developer = Developer::findOne($id)) === null) {
            echo "Застройщик с ID $id не найден\n";
            return self::RETURN_ERROR_NOT_FOUND;
        }
        
        if (is_null($import = $developer->import)) {
            echo "Для застройщика не настроен импорт\n";
            return self::RETURN_ERROR_NO_IMPORT;
        }

        $algorithm = $import->algorithmAsObject;

        $data = $algorithm->getAndParse($import->endpoint);
		
		$dataTree = array();
		
		foreach ($data['newbuildingComplexes'] as $nbc_key => $newbuildingComplex) {
			$newbuildingComplexFeedName = $newbuildingComplex['name'];
			
			foreach ($data['newbuildings'] as $nb_key => $newbuilding) {
				if ($newbuilding['objectId'] === $nbc_key) {
					$newbuildingFeedName = $newbuilding['name'];
					
					$dataTree[$newbuildingComplexFeedName][$newbuildingFeedName] = [
						//$newbuildingFeedName => [
							'flats' => array(),
							'flats_in_feed_amount' => 0,
							'flats_in_feed_numbers' => array()
						//]
					];
					
					$newbuildingFlatCounter = 0;
					
					foreach ($data['flats'] as $flat) {
						if ($flat['houseId'] === $nb_key) {
							$newbuildingFlatCounter += 1;
							//$dataTree[$newbuildingComplexFeedName][$newbuildingFeedName]['flats'][] = $flat;
							$dataTree[$newbuildingComplexFeedName][$newbuildingFeedName]['flats_in_feed_amount'] = $newbuildingFlatCounter;
							$dataTree[$newbuildingComplexFeedName][$newbuildingFeedName]['flats_in_feed_numbers'][] = $flat['number'];
						}
					}
				}
			}
		}
		
		// list positions from feeds
        // var_dump($dataTree);
		foreach ($dataTree as $newbuildingComplexName => $buildings) {
			echo '----- '.$newbuildingComplexName.' -----';
			// var_dump($buildings);
			echo PHP_EOL;
			foreach ($buildings as $buildingName => $buildingData) {
				echo $buildingName;
				echo PHP_EOL;
				echo 'передаётся '.$buildingData['flats_in_feed_amount'].' квартир';
				echo PHP_EOL;
			}
		}
    }
    
}
<?php

namespace app\commands;

use yii\console\Controller;
use app\models\Application;
use app\models\Agency;
use app\models\service\Developer;
use app\models\NewbuildingComplex;

class StatController extends Controller
{
    /**
     * method to show amounts of applications by agency
     */
    public function actionAppsByAgency() {
        $appsByAgencies = [];
        $applications = Application::find()->all();

        foreach ($applications as $application) {
            if (!array_key_exists($application->applicant->agency_id, $appsByAgencies)) {
                $appsByAgencies[$application->applicant->agency_id] = [];
            }
            array_push($appsByAgencies[$application->applicant->agency_id], $application->id);
        }

        foreach ($appsByAgencies as $agencyId => $apps) {
            $agency = Agency::findOne($agencyId);
            if ($agency !== null) {
                echo $agency->name.' - ';
                echo count($apps);
                echo PHP_EOL;
            }
        }
    }

    /**
	 * method to compare positions in feed (XML) with positions in DataBase
	 * in order to find out 'suspended' positions (that contained in data base, but not contained in XML-feed)
	 */
	public function actionFeedInquiry() {

		$developers = Developer::find()->all();
		
		foreach ($developers as $developer) {
			
			echo "==================================\n";
			echo $developer->name."\n";
			echo "==================================\n";
	
			if (is_null($import = $developer->import)) {
				echo "Для застройщика не настроен импорт\n";
				return self::RETURN_ERROR_NO_IMPORT;
			}

			$algorithm = $import->algorithmAsObject;

			$endpoints = explode(', ', $import->endpoint);

			foreach ($endpoints as $endpoint) {

                $endpointParams = explode('|algo:', $endpoint);
                if(count($endpointParams) > 1 && !empty($endpointParams[1])) {
                    $algorithm = $import->getAlgorithmAsObject($endpointParams[1]);
                    $data = $algorithm->getAndParse($endpointParams[0]);
                } else {
                    $data = $algorithm->getAndParse($endpoint);
                }

				// $data = $algorithm->getAndParse($endpoint);
				
				$dataTree = array();
				
				foreach ($data['newbuildingComplexes'] as $nbc_key => $newbuildingComplex) {
					$newbuildingComplexFeedName = $newbuildingComplex['name'];
					
					foreach ($data['newbuildings'] as $nb_key => $newbuilding) {
						if ($newbuilding['objectId'] === $nbc_key) {
							$newbuildingFeedName = $newbuilding['name'];
							
							$dataTree[$newbuildingComplexFeedName][$newbuildingFeedName] = [
								'flats' => array(),
								'flats_in_feed_amount' => 0,
								'flats_in_feed_numbers' => array()
							];
							
							$newbuildingFlatCounter = 0;
							
							foreach ($data['flats'] as $flat) {
								if ($flat['houseId'] === $nb_key) {
									$newbuildingFlatCounter += 1;
									$dataTree[$newbuildingComplexFeedName][$newbuildingFeedName]['flats'][] = $flat;
									$dataTree[$newbuildingComplexFeedName][$newbuildingFeedName]['flats_in_feed_amount'] = $newbuildingFlatCounter;
									$dataTree[$newbuildingComplexFeedName][$newbuildingFeedName]['flats_in_feed_numbers'][] = $flat['number'];
								}
							}
						}
					}
				}
				
				// list positions from feeds, compare with positions on website
				// var_dump($dataTree);
				foreach ($dataTree as $newbuildingComplexName => $buildings) {
					$complexFromDataBase = (new NewbuildingComplex())->find()->byFeedName($newbuildingComplexName)->one();
					$buildingsFromDataBase = $complexFromDataBase->newbuildings;
					//echo '----- '.$newbuildingComplexName.' -----';
					echo '***** '.$complexFromDataBase->name.' *****';
					echo PHP_EOL;
					foreach ($buildings as $buildingName => $buildingData) {
						foreach($buildingsFromDataBase as $building) {
							if ($building->feed_name == $buildingName) {
								$buildingFromDataBase = $building;
							}
						}
						echo $buildingFromDataBase->name.' - ';
						echo 'передаётся '.$buildingData['flats_in_feed_amount'].' квартир; ';
						echo 'на сайте '.$buildingFromDataBase->activeFlatsAmount.' квартир;';
						if ($buildingFromDataBase->activeFlatsAmount > $buildingData['flats_in_feed_amount']) {
							echo ' - ВНИМАНИЕ, ПРОВЕРИТЬ!';
						}
						echo PHP_EOL;
					}
				}
			}
		}
    }
}
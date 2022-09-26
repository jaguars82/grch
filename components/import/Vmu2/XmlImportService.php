<?php

namespace app\components\import\Vmu2;

use app\components\exceptions\AppException;
use app\components\import\ImportServiceInterface;
use app\models\Flat;

/**
 * Service for importing flat's data from xml-feed for developer "ВМУ-2"
 */
class XmlImportService implements ImportServiceInterface
{

    /**
     * {@inheritdoc}
     */
    public function getAndParse($endpoint)
    {
        $client = new \yii\httpclient\Client([
            'transport' => 'yii\httpclient\CurlTransport'
        ]);

        $fileContent = $client->createRequest()
            ->setMethod('GET')
            ->setUrl($endpoint)
            ->send()
            ->content;

        $flatsData = new \SimpleXMLElement($fileContent, false);

        return $this->parseData($flatsData);
    }

    /**
     * {@inheritdoc}
     */
    public function isSupportAuto()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isSupportManual()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function parseFile($filename)
    {
        throw new \Exception('Not suppored yet');
    }

    /**
     * Check that object data is valid
     *
     * @param mixed $offer
     * @throws AppException
     */
    private function checkBuildingName($building)
    {
        if (!isset($building['Имя']) || (string) $building['Имя'] == '') {
            throw new AppException("Нет данных о названии позиции");
        }
    }

    /**
     * Get current entity ID
     *
     * @param array $entities
     * @param string $entityName
     * @return int
     */
   private function getCurrentEntityId($entities, $entityName)
    {
        $currentEntityId = -1;

        foreach ($entities as $key => $entity) {
            if ($entity['name'] == $entityName) {
                $currentEntityId = $key;
                break;
            }
        }

        return $currentEntityId;
    }

	/**
	 * Get house deadline
	 *
	 * @param mixed $house
	 * @return int
	 */
	function getDeadline($house)
	{
		$dateElements = explode('.', $house['ПлановаяДатаВводаВЭксплуатацию']);
		
        $deadline = '';

        if (is_array($dateElements) && count($dateElements) == 3) {
            $deadline = \Yii::$app->formatter->asDate($dateElements[0].'.' . $dateElements[1] . '.' . $dateElements[2], 'php:Y-m-d H:i:s');
        }

		return $deadline;
	}

	/*private function getFlatLayout($flat)
	{
		if (!isset($flat->plan)) {
			return NULL;
		}
		$layout = (string)$flat->plan;
		if ($layout == '') {
			return NULL;
		}

		if (strpos($layout, 'Null')) {
			$layout = str_replace('Null', 'null', $layout);
		}
		return $layout;
	}*/

    /**
     * Parse flats data
     *
     * @param array $data
     * @return array
     * @throws AppException
     */
    private function parseData($data)
    {
        $complexBuildingMap = [
            'ЖД на Сакко и Ванцетти' => ['ул.Сакко и Ванцетти,82'],
            'Микрорайон Боровое' => [
                'ул.Сельская, 2з, поз.19, 1 этап',
                'ул.Сельская, 2В, поз.26',
                'ул.Сельская, 2м, поз.14',
                'ул.Сельская, 2В, поз.25',
            ]
        ];

        $flats = [];
        $objects = [];
        $houses = [];
        $flatsByNumber = [];
        $currentFlatId = 0;
        $objectId = 0;
        $houseId = 0;

        $floorLayoutsData = [];
        $layoutCount = 0;

        if (!isset($data->Район)) {
            throw new AppException("Нет данных о квартирах");
        }

		// Парсим ЖК
        foreach ($data->Район as $district) {

            foreach ($district->Позиция as $building) {

                // echo '<pre>'; var_dump($building['Имя']); echo '</pre>'; die();

                // check if a position has name
                $this->checkBuildingName($building);

                // check if a position exists in 'complexBuildingMap'
                $building_is_in_map = false;
                $objectName = '';
                foreach ($complexBuildingMap as $complexName => $complexBuildings) {
                    if (in_array($building['Имя'], $complexBuildings)) {
                        $building_is_in_map = true;
                        $objectName = (string)$complexName;
					}
                }


                if ($building_is_in_map === true && ($currentObjectId = $this->getCurrentEntityId($objects, $objectName)) == -1) {
                    $address = (string)$building['Имя'];

                    $objects[$objectId] = [
                        'name' => $objectName,
                        'address' => $address,
                        'district' => NULL,
                    ];

                    $currentObjectId = $objectId++;
                }

                // Парсим позиции
                //foreach ($complex->buildings->building as $building) {

                    $houseName = (string)$building['Имя'];
                    $deadline = $this->getDeadline($building);

                    $houses[$houseId] = [
                        'objectId' => $currentObjectId,
                        'name' => $houseName,
                        'total_floor' => (int)$building['КоличествоЭтажейВдоме'],
                        'deadline' => $deadline,
                        'material' => '',
                    ];

                    $currentHouseId = $houseId++;

                    /*if (!isset($building->flats->flat)) {
                        continue;
                    }*/

                    /*
                    $lastFlatNumber = 1;
                    $lastFlatFloor = 1;
                    $section = 1;
                    */

                    // pass through entrances
                    foreach ($building->Подъезд as $entrance) {
                        $section = (int)$entrance['Номер'];

                        // pass through floors
                        foreach ($entrance->Этаж as $level) {
                            $floor = (int)$level['Номер'];

                            // parse flats
                            foreach ($level->Квартира as $flat) {
                                $_flat = [
                                    'houseId' => $currentHouseId,
                                    'number' => $flat['Номер'],
                                    'section' => $section,
                                    'floor' => $floor,
                                    'area' => (float)$flat['ОбщаяПлощадь'],
                                    'rooms' => $flat['КоличествоКомнат'] == 'Свободная планировка' ? 1 : (int)$flat['КоличествоКомнат'],
                                    'unit_price_cash' => (float)$flat['ЦенаЗаМетр'],
                                    'price_cash' => (float)preg_replace("/[^,.0-9]/", '', $flat['СтоимостьПомещения']),
                                    // 'status' => $this->getStatus((string)$flat->window_view),
                                    'status' => empty($flat['СтоимостьПомещения']) ? Flat::STATUS_SOLD : Flat::STATUS_SALE,
                                    'layout' => '',
                                ];
								
								/*echo '|';
								echo preg_replace("/[^,.0-9]/", '', $flat['СтоимостьПомещения']);
								echo '|';*/

                                $flats[$currentFlatId] = $_flat;
                                //$flatsByNumber[$currentObjectId][$currentHouseId][$flat['Номер']][] = $currentFlatId;
                                $currentFlatId++;
                            }
                        }
                    }

                    /*foreach ($building->flats->flat as $flat) {
                        $unitPrice = ($flat->area != 0) ? ((int)$flat->price / (float)$flat->area) : 0;
                        $layout =  $this->getFlatLayout($flat);

                        $currentFlatNumber = (int)$flat->apartment;
                        $currentFlatFloor = (int)$flat->floor;

                        if (($lastFlatFloor - $currentFlatFloor) > 1) {
                            $section++;
                        }

                        $_flat = [
                            'houseId' => $currentHouseId,
                            'number' => (int)$flat->apartment,
                            'section' => $section,
                            'floor' => (int)$flat->floor,
                            'area' => (float)$flat->area,
                            'rooms' => (int)$flat->room,
                            'unit_price_cash' => $unitPrice,
                            'price_cash' => (float)$flat->price,
                            // 'status' => $this->getStatus((string)$flat->window_view),
                            'status' => Flat::STATUS_SALE,
                            'layout' => $layout,
                        ];

                        $flats[$currentFlatId] = $_flat;
                        $flatsByNumber[$currentObjectId][$currentHouseId][(int)$flat->apartment][] = $currentFlatId;
                        $currentFlatId++;

                        $lastFlatFloor = $currentFlatFloor;
                    }*/
                //}
            }
            
		}

		//var_dump($objects); die();

        $floorLayouts = [];

        return [
            'floorLayouts' => $floorLayouts,
            'newbuildingComplexes' => $objects,
            'newbuildings' => $houses,
            'flats' => $flats,
        ];
    }
}
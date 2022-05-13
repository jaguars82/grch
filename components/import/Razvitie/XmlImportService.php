<?php

namespace app\components\import\Razvitie;

use app\components\exceptions\AppException;
use app\components\import\ImportServiceInterface;
use app\models\Flat;

/**
 * Service for importing flat's data from xml-feed for developer "Инстеп"
 */
class XmlImportService implements ImportServiceInterface
{
    /**
     * Status value for string
     *
     * @var integer
     */
    protected $status = [
        'Available' => Flat::STATUS_SALE,
		'Reserved' => Flat::STATUS_RESERVED,
		'Unavailable' => Flat::STATUS_SOLD,
    ];

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
        throw new \Excetion('Not suppored yet');
    }

    /**
     * Get flat status from string
     *
     * @param int $flatCol Column of cell
     * @param int $flatRow Row of cell
     * @return boolean
     */
    protected function getStatus($value)
    {
        return (isset($this->status[$value])) ? $this->status[$value] : Flat::STATUS_SOLD;
    }

    /**
     * Check that object data is valid
     *
     * @param mixed $offer
     * @throws AppException
     */
    private function checkObjectData($complex)
    {
        if (!isset($complex->name) || (string) $complex->name == '') {
            throw new AppException("Нет данных о названии жилого комплекса");
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
	private function getDeadline($house)
	{
		$month = 1;
		$year = (int)$house->built_year;
		$quarter = (int)$house->ready_quarter;

		switch ($quarter) {
			case 1:
				$month = 1;
				break;
			case 2:
				$month = 4;
				break;
			case 3:
				$month = 7;
				break;
			case 4:
				$month = 10;
				break;
		}

		$deadline = \Yii::$app->formatter->asDate('01.' . $month . '.' . $year, 'php:Y-m-d H:i:s');

		return $deadline;
	}

	private function getFlatLayout($flat)
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
	}

    /**
     * Parse flats data
     *
     * @param array $data
     * @return array
     * @throws AppException
     */
    private function parseData($data)
    {
        $flats = [];
        $objects = [];
        $houses = [];
        $flatsByNumber = [];
        $currentFlatId = 0;
        $objectId = 0;
        $houseId = 0;

        $floorLayoutsData = [];
        $layoutCount = 0;

        if (!isset($data->House)) {
            throw new AppException("Нет данных о квартирах");
        }

		// Парсим ЖК
        foreach ($data->House as $complex) {
            $objectName = (string)$complex->Name;

            // echo stripos($complexName, 'секция');
            // continue;

            // temporary fix
            if (strpos($objectName, 'ЖК "Молодежный" 2') !== false) {
				$objectName = 'ЖК "Молодежный" 1';
			}

            if (mb_stripos($objectName, 'секция') !== false) {
                $objectName = mb_substr($objectName, 0, mb_stripos($objectName, 'секция') - 1);
            }

            if (mb_stripos($objectName, 'этап') !== false) {
                $objectName = mb_substr($objectName, 0, mb_stripos($objectName, 'этап') - 1);
            }

            if (mb_stripos($objectName, 'поз') !== false) {
                $objectName = mb_substr($objectName, 0, mb_stripos($objectName, 'поз') - 1);
            }

            if (mb_stripos($objectName, '(') !== false) {
                $objectName = mb_substr($objectName, 0, mb_stripos($objectName, '(') - 1);
            }

            if (($currentObjectId = $this->getCurrentEntityId($objects, $objectName)) == -1) {
                $objects[$objectId] = [
                    'name' => $objectName,
                ];

                $currentObjectId = $objectId++;
            }


            // echo $complex->Name . ' - ' . $objectName . PHP_EOL;
            // continue;

            foreach ($complex->Sections->Section as $house) {
                $houseName = (string)$house->attributes()->name;
                $houses[$houseId] = [
                    'objectId' => $currentObjectId,
                    'name' => $houseName,
                    'total_floor' => (int)$complex->Floors,
                ];

                $currentHouseId = $houseId++;

                $lastFlatNumber = 1;
                $lastFlatFloor = 1;
                $section = 1;

                if (!isset($house->Apps->App)) {
                    continue;
                }

                foreach ($house->Apps->App as $flat) {

                    if ($flat->Rooms == 'ст') { 
                        continue;
                    }

                    $unitPrice = ($flat->Square != 0) ? ((int)$flat->BaseAmount / (float)$flat->Square) : 0;

                    $currentFlatNumber = (int)$flat->AppNumber;
                    $currentFlatFloor = (int)$flat->Floor;

                    if (($lastFlatFloor - $currentFlatFloor) > 1) {
                        $section++;
                    }

                    $_flat = [
                        'houseId' => $currentHouseId,
                        'number' => (int)$flat->AppNumber,
                        'section' => $section,
                        'floor' => (int)$flat->Floor,
                        'area' => (float)$flat->Square,
                        'rooms' => (int)$flat->Rooms,
                        'unit_price_cash' => $unitPrice,
                        'price_cash' => (float)$flat->BaseAmount,
                        'status' => $this->getStatus((string)$flat->Status),
                    ];


                    $flats[$currentFlatId] = $_flat;
                    $flatsByNumber[$currentObjectId][$currentHouseId][(int)$flat->apartment][] = $currentFlatId;
                    $currentFlatId++;

                    $lastFlatFloor = $currentFlatFloor;
                }
            }
		}

        // print_r($objects);



        $floorLayouts = [];

        return [
            'floorLayouts' => $floorLayouts,
            'newbuildingComplexes' => $objects,
            'newbuildings' => $houses,
            'flats' => $flats,
        ];
    }
}
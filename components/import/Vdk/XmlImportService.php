<?php

namespace app\components\import\Vdk;

use app\components\exceptions\AppException;
use app\components\import\ImportServiceInterface;
use app\models\Flat;

/**
 * Service for importing flat's data from xml-feed for developer "ВДК"
 */
class XmlImportService implements ImportServiceInterface
{
    const BUILD_YEAR_FIELD = 'built-year';
    const FLOOR_TOTAL_FIELD = 'floors-total';
    const LOCALITY_NAME_FIELD = 'locality-name';
    const PRICE_METER_FIELD = 'price-meter';
    const READY_QUARTER_FIELD = 'ready-quarter';
    const SECTION_FIELD = 'building-section';
    const STATUS_FIELD = 'status-humanized';

    /**
     * Status value for string
     *
     * @var integer
     */

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

        if (!isset($data->complex)) {
            throw new AppException("Нет данных о квартирах");
        }

		// Парсим ЖК
        foreach ($data->complex as $complex) {

            $this->checkObjectData($complex);
            $objectName = (string)$complex->name;

			if (!isset($complex->buildings->building)) {
				continue;
			}

            if (($currentObjectId = $this->getCurrentEntityId($objects, $objectName)) == -1) {
                $address = (string)$complex->address;

                $objects[$objectId] = [
                    'name' => $objectName,
                    'address' => $address,
                    'district' => NULL,
                ];

                $currentObjectId = $objectId++;
            }

			// Парсим позиции
			foreach ($complex->buildings->building as $building) {

				$houseName = (string)$building->name;
				$deadline = $this->getDeadline($building);

				$houses[$houseId] = [
					'objectId' => $currentObjectId,
					'name' => $houseName,
					'total_floor' => (int)$building->floors,
					'deadline' => $deadline,
					'material' => (string)$building->building_type,
				];

				$currentHouseId = $houseId++;

				if (!isset($building->flats->flat)) {
					continue;
				}

				$lastFlatNumber = 1;
				$lastFlatFloor = 1;
				$section = 1;


				foreach ($building->flats->flat as $flat) {
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
		                'status' => Flat::STATUS_SALE,
						'layout' => $layout,
		            ];

					$flats[$currentFlatId] = $_flat;
		            $flatsByNumber[$currentObjectId][$currentHouseId][(int)$flat->apartment][] = $currentFlatId;
		            $currentFlatId++;

					$lastFlatFloor = $currentFlatFloor;
				}
			}
		}



        $floorLayouts = [];

        return [
            'floorLayouts' => $floorLayouts,
            'newbuildingComplexes' => $objects,
            'newbuildings' => $houses,
            'flats' => $flats,
        ];
    }
}
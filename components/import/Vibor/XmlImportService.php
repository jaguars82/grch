<?php

namespace app\components\import\Vibor;

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
		'Открыт в продажу' => Flat::STATUS_SALE,
        'Повторная продажа' => Flat::STATUS_SALE,
		'Продан' => Flat::STATUS_SOLD,
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

        @$flatsData = new \SimpleXMLElement($fileContent, false);

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

        if (!isset($data->home)) {
            throw new AppException("Нет данных о квартирах");
        }

		// Парсим позиции
		$statuses = [];
        foreach ($data->home as $home) {

			$objectName = (string)$home->attributes()->ComplexName;

            if (($currentObjectId = $this->getCurrentEntityId($objects, $objectName)) == -1) {
                $objects[$objectId] = [
                    'name' => $objectName,
                ];

                $currentObjectId = $objectId++;
            }

			$houseName = (string)$home->attributes()->HomeName;

			$houses[$houseId] = [
				'objectId' => $currentObjectId,
				'name' => $houseName,
				'total_floor' => (int)$home->attributes()->HomeFloorCount,
			];
			$currentHouseId = $houseId++;

			foreach ($home->flat as $flat) {
				$unitPrice = ($flat->FlatArea != 0) ? ((int)$flat->FlatPrice / (float)$flat->FlatArea) : 0;


				$_flat = [
					'houseId' => $currentHouseId,
					'number' => (int)$flat->FlatNumber,
					'floor' => (int)$flat->FlatFloor,
					'area' => (float)$flat->FlatArea,
					'rooms' => (int)$flat->FlatRoomCount,
					'unit_price_cash' => $unitPrice,
					'price_cash' => (float)$flat->FlatPrice,
					'status' => $this->getStatus((string)$flat->FlatStatus),
				];

				$flatsByNumber[(int)$flat->FlatNumber] = $_flat;
			}

			ksort($flatsByNumber);

			$lastFlatNumber = 1;
			$lastFlatFloor = 1;
			$section = 1;
			foreach ($flatsByNumber as &$_flat) {
				$currentFlatNumber = $_flat['number'];
				$currentFlatFloor = $_flat['floor'];
				if (($lastFlatFloor - $currentFlatFloor) > 1) {
					$section++;
				}
				$lastFlatFloor = $currentFlatFloor;

				$_flat['section'] = $section;
				$flats[$currentFlatId] = $_flat;

				$currentFlatId++;
			}

			$flatsByNumber = [];
		}

        return [
            'floorLayouts' => [],
            'newbuildingComplexes' => $objects,
            'newbuildings' => $houses,
            'flats' => $flats,
        ];
    }
}
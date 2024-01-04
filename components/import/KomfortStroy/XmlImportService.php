<?php

namespace app\components\import\KomfortStroy;

use app\components\exceptions\AppException;
use app\components\import\ImportServiceInterface;
use app\models\Flat;

/**
 * Service for importing flat's data from xml-feed for developer "Инстеп"
 */
class XmlImportService implements ImportServiceInterface
{
    const BUILD_YEAR_FIELD = 'built-year';
    const FLOOR_TOTAL_FIELD = 'floors-total';
    const LOCALITY_NAME_FIELD = 'locality-name';
    const READY_QUARTER_FIELD = 'ready-quarter';

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
        return (isset($this->status[$value])) ? $this->status[$value] : -1;
    }

    /**
     * Check that flat data is valid
     *
     * @param mixed $offer
     * @param string $objectName
     * @param string $houseName
     * @throws AppException
     */
    private function checkFlatData($offer, $objectName, $houseName)
    {
        if (!isset($offer->location->apartment) || (string)$offer->location->apartment == '') {
            throw new AppException("Нет данных о номере квартиры (в жилом комплексе {$objectName}, позиция {$houseName})");
        }

        $flatNumber = (int)$offer->location->apartment;
        $debugData = "(в жилом комплексе {$objectName}, позиция {$houseName}, квартира №{$flatNumber})";

        if (!isset($offer->floor) || (string)$offer->floor == '') {
            throw new AppException("Нет данных об этаже квартиры $debugData");
        }

        if (!isset($offer->area) || !isset($offer->area->value) || (string)$offer->area->value == '') {
            throw new AppException("Нет данных о площади квартиры $debugData");
        }

        if (!isset($offer->{'commercial-type'}) && (!isset($offer->rooms) || (string)$offer->rooms == '')) {
            throw new AppException("Нет данных о количестве комнат в квартире $debugData");
        }

    }

    /**
     * Check that house data is valid
     *
     * @param mixed $offer
     * @param string $objectName
     * @throws AppException
     */
    private function checkHouseData($offer, $objectName)
    {
        if (!isset($offer->{'yandex-house-id'})) {
            throw new AppException("Нет данных о позиции (в жилом комплексе {$objectName})");
        }

        if ((string)$offer->{'yandex-house-id'} == '') {
            throw new AppException("Нет данных о названии позиции (в жилом комплексе {$objectName})");
        }
    }

    /**
     * Check that object data is valid
     *
     * @param mixed $offer
     * @throws AppException
     */
    private function checkObjectData($offer)
    {
        if (!isset($offer->{'building-name'})) {
            throw new AppException("Нет данных о жилом комплексе");
        }

        if ((string) $offer->{'building-name'} == '') {
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
     * Get flat extra data
     *
     * @param mixed $offer
     * @return array
     */
    private function getFlatExtraData($offer)
    {
        $extraData = [];
        $deadline = NULL;

        foreach ($offer as $key => $value) {

            if (!in_array($key, [
                'location', 'price', 'area', 'building-name', 'yandex-building-id', 'yandex-house-id', 'built-year', 'ready-quarter', 'building-state', 'rooms', 'floor', 'floors-total', 
            ])) {
                if (count($value)) {
                    $extraData[$key] = (array)$value;
                } elseif ((string)$value != '') {
                    $extraData[$key] = (string)$value;
                }
            }
        }
		
		$deadline = $this->validateDate($deadline) ? $deadline : null;

        return [$extraData, $deadline];
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

        if (!isset($data->offer)) {
            throw new AppException("Нет данных о квартирах");
        }

        foreach ($data->offer as $offer) {
            if (isset($offer->{'commercial-type'})) continue;
            
            list($extraData, $deadline) = $this->getFlatExtraData($offer);

            $this->checkObjectData($offer);
            $objectName = (string)$offer->{'building-name'};

            if (($currentObjectId = $this->getCurrentEntityId($objects, $objectName)) == -1) {
                $address = (string)$offer->location->country
                        . ', ' . (string)$offer->location->region
                        . ', ' . (string)$offer->location->{self::LOCALITY_NAME_FIELD}
                        . ', ' . (string)$offer->location->address;

                $district = ((string)$offer->location->{'sub-locality-name'} !== '') ? (string)$offer->location->{'sub-locality-name'} : NULL;

                $objects[$objectId] = [
                    'name' => $objectName,
                    'address' => $address,
                    'district' => $district,
                ];

                $currentObjectId = $objectId++;
            }

            $this->checkHouseData($offer, $address);
            $houseName = (string)$offer->{'yandex-house-id'};

            if (($currentHouseId = $this->getCurrentEntityId($houses, $houseName)) == -1) {
                $houses[$houseId] = [
                    'objectId' => $currentObjectId,
                    'name' => $houseName,
                    'total_floor' => (string)$offer->{self::FLOOR_TOTAL_FIELD},
                    'deadline' => $deadline,
                ];

                $currentHouseId = $houseId++;
            }

            $this->checkFlatData($offer, $objectName, $houseName);

            $flat = [
                'houseId' => $currentHouseId,
                'number' => (int)$offer->location->apartment,
                'section' => 1, // (place flat to section 1 b3cause we don't have srction field in a feed)
                'floor' => (int)$offer->floor,
                'area' => (float)$offer->area->value,
                'rooms' => (int)$offer->rooms,
                'unit_price_cash' => !empty($offer->area->value) ? (float)$offer->price->value / (float)$offer->area->value : 0,
                'price_cash' => (float)$offer->price->value,
                //'status' => $this->getStatus((string)$offer->status),
                'status' => 0,
                'extra_data' => count($extraData) ? json_encode($extraData)/*$extraData*/ : NULL,
            ];

            foreach($offer->image as $image) {
                foreach($image->attributes() as $attribute => $value) {
                    if ((string)$attribute === 'type' && (string)$value === 'plan') {
                        $flat['layout'] = (string)$image;
                        $layoutCount++;
                        break 2;
                    }
                }
            }

            $flats[$currentFlatId] = $flat;
            $flatsByNumber[$currentObjectId][$currentHouseId][(int)$offer->number][] = $currentFlatId;
            $currentFlatId++;

            foreach($offer->image as $image) {
                foreach($image->attributes() as $attribute => $value) {
                    if ((string)$attribute === 'type' && (string)$value === 'plan floor') {
                        $floorLayoutsData[$currentHouseId][1][(int)$offer->floor] = [
                            'houseId' => $currentHouseId,
                            'image' => (string)$image,
                        ];
                        break 2;
                    }
                }
            }
        }

        $flats = $this->processCompositeFlats($flats, $flatsByNumber);

        $floorLayouts = [];
        foreach($floorLayoutsData as $house => $sections) {
            foreach($sections as $section => $floors) {
                foreach($floors as $floor => $floorData) {
                    $floorLayouts[] = [
                        'houseId' => $house,
                        'section' => $section,
                        'floor' => $floor,
                        'image' => $floorData['image'],
                    ];
                }
            }
        }

        return [
            'floorLayouts' => $floorLayouts,
            'newbuildingComplexes' => $objects,
            'newbuildings' => $houses,
            'flats' => $flats,
        ];
    }

    /**
     * Process composite flats
     *
     * @param array $flats
     * @param array $flatsByNumber
     * @return array
     */
    private function processCompositeFlats($flats, $flatsByNumber)
    {
        $complexObjectId = 0;

        foreach ($flatsByNumber as $objectItem => $housesItem) {
            foreach ($housesItem as $houseItem => &$flatNumbers) {
                $flatsByNumber[$objectItem][$houseItem] = array_filter($flatNumbers, function($flats){
                    return count($flats) > 1;
                });

                foreach ($flatsByNumber[$objectItem][$houseItem] as $flatNumber => $flatsItem) {
                    foreach ($flatsByNumber[$objectItem][$houseItem][$flatNumber] as $flatId) {
                        $flats[$flatId]['compositeFlatId'] = $complexObjectId;
                    }
                    $complexObjectId++;
                }
            }
        }

        return $flats;
    }
	
	private function validateDate($date, $format = 'Y-m-d H:i:s')
	{
		$year = date("Y",strtotime($date));
		
		if ($year < 2000) {
			return false;
		}
		
		$d = \DateTime::createFromFormat($format, $date);
		return $d && $d->format($format) == $date;
	}
}

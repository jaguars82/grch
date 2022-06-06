<?php

namespace app\components\import\Instep;

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
    const PRICE_METER_FIELD = 'price-meter';
    const READY_QUARTER_FIELD = 'ready-quarter';
    const SECTION_FIELD = 'building-section';
    const STATUS_FIELD = 'status-humanized';

    /**
     * Status value for string
     *
     * @var integer
     */
    protected $status = [
        'AVAILABLE' => Flat::STATUS_SALE,
        'BOOKED_bitrix_5f8056e3c179b' => Flat::STATUS_SOLD,
        'SOLD' => Flat::STATUS_SOLD,
        'BOOKED' => Flat::STATUS_RESERVED,
        'UNAVAILABLE' => Flat::STATUS_RESERVED,
        'BOOKED_bitrix_5e1c7571d8aff' => Flat::STATUS_RESERVED,

        'Свободно' => Flat::STATUS_SALE,
        'Продано' => Flat::STATUS_SOLD,
        'Бронь' => Flat::STATUS_RESERVED,
        'Не для продажи' => Flat::STATUS_RESERVED,
        'Ожидает Переоценки' => Flat::STATUS_RESERVED,
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
        if (!isset($offer->number) || (string)$offer->number == '') {
            throw new AppException("Нет данных о номере квартиры (в жилом комплексе {$objectName}, позиция {$houseName})");
        }

        $flatNumber = (int)$offer->number;
        $debugData = "(в жилом комплексе {$objectName}, позиция {$houseName}, квартира №{$flatNumber})";

        if (!isset($offer->{self::SECTION_FIELD}) || (string)$offer->{self::SECTION_FIELD} == '') {
            throw new AppException("Нет данных о подъезде квартиры $debugData");
        }

        if (!isset($offer->floor) || (string)$offer->floor == '') {
            throw new AppException("Нет данных об этаже квартиры $debugData");
        }

        if (!isset($offer->area) || !isset($offer->area->value) || (string)$offer->area->value == '') {
            throw new AppException("Нет данных о площади квартиры $debugData");
        }

        if (!isset($offer->rooms) || (string)$offer->rooms == '') {
            throw new AppException("Нет данных о количестве комнат в квартире $debugData");
        }

        if (!isset($offer->status) || (string)$offer->status == '') {
            throw new AppException("Нет данных о статусе квартиры $debugData");
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
        if (!isset($offer->house)) {
            throw new AppException("Нет данных о позиции (в жилом комплексе {$objectName})");
        }

        if (!isset($offer->house->name) || (string)$offer->house->name == '') {
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
        if (!isset($offer->object)) {
            throw new AppException("Нет данных о жилом комплексе");
        }

        if (!isset($offer->object->name) || (string) $offer->object->name == '') {
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
            if ($key == 'custom-field' && (string)$value->value != '') {
                if ((string)$value->name == 'Дата сдачи дома') {
                    try {
                        $deadline = \Yii::$app->formatter->asDate((string)$value->value, 'php:Y-m-d H:i:s');
                    } catch (\Exception $ex) {}
                }

                $extraData[(string)$value->name] = (string)$value->value;
            }

            if (!in_array($key, [
                'object', 'house', 'number', self::SECTION_FIELD, 'floor', 'area', 'rooms', self::PRICE_METER_FIELD, 'price', 'status', 'status_id', 'status-humanized', 'image',
                'property_type', 'custom-field',
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
            list($extraData, $deadline) = $this->getFlatExtraData($offer);

            $this->checkObjectData($offer);
            $objectName = preg_replace('/^Инстеп./', '', (string)$offer->object->name);

            if (($currentObjectId = $this->getCurrentEntityId($objects, $objectName)) == -1) {
                $address = (string)$offer->object->location->country
                        . ', ' . (string)$offer->object->location->region
                        . ', ' . (string)$offer->object->location->{self::LOCALITY_NAME_FIELD}
                        . ', ' . (string)$offer->object->location->address;

                $district = ((string)$offer->object->location->district !== '') ? (string)$offer->object->location->district : NULL;

                $objects[$objectId] = [
                    'name' => $objectName,
                    'address' => $address,
                    'district' => $district,
                ];

                $currentObjectId = $objectId++;
            }

            $this->checkHouseData($offer, $address);
            $houseName = (string)$offer->house->name;

            if (($currentHouseId = $this->getCurrentEntityId($houses, $houseName)) == -1) {
                $houses[$houseId] = [
                    'objectId' => $currentObjectId,
                    'name' => $houseName,
                    'total_floor' => (string)$offer->house->{self::FLOOR_TOTAL_FIELD},
                    'deadline' => $deadline,
                ];

                $currentHouseId = $houseId++;
            }

            $this->checkFlatData($offer, $objectName, $houseName);

            $flat = [
                'houseId' => $currentHouseId,
                'number' => (int)$offer->number,
                'section' => (int)$offer->{self::SECTION_FIELD},
                'floor' => (int)$offer->floor,
                'area' => (float)$offer->area->value,
                'rooms' => (int)$offer->rooms,
                'unit_price_cash' => (float)$offer->{self::PRICE_METER_FIELD}->value,
                'price_cash' => (float)$offer->price->value,
                'status' => $this->getStatus((string)$offer->status),
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
                        $floorLayoutsData[$currentHouseId][(int)$offer->{self::SECTION_FIELD}][(int)$offer->floor] = [
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

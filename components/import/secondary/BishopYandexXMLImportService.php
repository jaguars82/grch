<?php

namespace app\components\import\secondary;

use app\components\exceptions\AppException;
use app\components\import\secondary\SecondaryImportServiceInterface;
use app\models\StreetType;

/**
 * Service for importing flat's data from xml-feed for developer "Новый Код"
 */
class BishopYandexXMLImportService implements SecondaryImportServiceInterface
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
     * Parse data
     *
     * @param array $data
     * @return array
     * @throws AppException
     */
    private function parseData($data)
    {
        $streetTypesDB = StreetType::getAllAsList();
        $advertisements = [];

        if (!isset($data->offer)) {
            throw new AppException("Нет данных об объявлениях");
        }

		// Parse advertisements
        foreach ($data->offer as $advertisement) {

            $advertisementId = (string)$advertisement['internal-id'];

            // try to figure out street_type_id, street name, building number
            if (!empty($advertisement->location->address)) {
                $addressParts = explode(', ', $advertisement->location->address);
                $addressPartsLength = count($addressParts);
                if ($addressPartsLength === 2) {
                    $buildingNumber = (string)$addressParts[1];
                }
                if ($addressPartsLength > 0) {
                    $streetParts = explode(' ', $addressParts[0]);
                    $streetPartsLength = count($streetParts);
                    if ($streetPartsLength > 1) {
                        foreach ($streetTypesDB as $typeId => $streetType) {
                            $streetPrefixCorrection = 'no_street_prefix';
                            if (mb_strtolower($streetType) == mb_strtolower($streetParts[array_key_last($streetParts)])) {
                                $streetTypeId = $typeId;
                                $streetPrefixCorrection = mb_strtolower($streetParts[array_key_last($streetParts)]);
                                array_pop($streetParts);
                                $streetName = str_replace($streetPrefixCorrection.' ', '', implode(' ', $streetParts));
                            }
                            
                        }
                    } elseif ($streetPartsLength === 1) {
                        $streetName = $streetParts[0];
                    }
                }
            }

            $location = [
                'country' => !empty($advertisement->location->country) ? (string)$advertisement->location->country : '',
                'region' => !empty($advertisement->location->region) ? (string)$advertisement->location->region : '',
                'district' => !empty($advertisement->location->district) ? (string)$advertisement->location->district : '',
                'locality_name' => !empty($advertisement->location->{'locality-name'}) ? (string)$advertisement->location->{'locality-name'} : '',
                'sub_locality_name' => !empty($advertisement->location->{'sub-locality-name'}) ? (string)$advertisement->location->{'sub-locality-name'} : '',
                'non_admin_sub_locality_name' => !empty($advertisement->location->{'non-admin-sub-locality'}) ? (string)$advertisement->location->{'non-admin-sub-locality'} : '',
                'address' => !empty($advertisement->location->address) ? (string)$advertisement->location->address : '',
                'latitude' => !empty($advertisement->location->latitude) ? (float)$advertisement->location->latitude : '',
                'longitude' => !empty($advertisement->location->longitude) ? (float)$advertisement->location->longitude : '',
            ];

            // balcony params
            $balcony_amount = '';
            $loggia_amount = '';
            if (!empty($advertisement->balcony)) {
                switch ($advertisement->balcony) {
                    case 'балкон':
                        $balcony_amount = 1;
                        break;
                    case 'лоджия':
                        $loggia_amount = 1;
                        break;
                    case '2 лоджии':
                        $loggia_amount = 2;
                        break;
                    case '2 балкона':
                        $balcony_amount = 2;
                        break;
                    /*default:
                        $balconyParts = explode(' ', $advertisement->balcony);
                        if (count($balconyParts) === 2) {
                            if ($amount = preg_match('/^-?\d+$/', $balconyParts[0])) {
                                switch ($balconyParts[1]) {
                                    case 'лоджии':
                                    case 'лоджий':
                                    case 'лоджия':
                                        $loggia_amount = (int)$amount;
                                        break;
                                    case 'балкона':
                                    case 'балконов':
                                    case 'балкон':
                                        $balcony_amount = (int)$amount;
                                        break;
                                }
                            }
                        }*/
                }
            }

            $agentPhones = array();
            foreach($advertisement->{'sales-agent'}->phone as $phone) {
                array_push($agentPhones, (string)$phone);
            }

            $agent = [
                'phones' => $agentPhones,
                'email' => !empty($advertisement->{'sales-agent'}->email) ? (string)$advertisement->{'sales-agent'}->email : '',
                'name' => !empty($advertisement->{'sales-agent'}->name) ? (string)$advertisement->{'sales-agent'}->name : '',
                'photo' => !empty($advertisement->{'sales-agent'}->photo) ? (string)$advertisement->{'sales-agent'}->photo : '',
            ];

            $windowviews = array();
            $viewYard = false;
            $viewStreet = false;
            foreach ($advertisement->{'window-view'} as $windowview) {
                if (!in_array($windowview, $windowviews)) {
                    switch ($windowview) {
                        case 'на улицу':
                            $viewStreet = true;
                            break;
                        case 'во двор':
                            $viewYard = true;
                            break;
                    }
                    array_push($windowviews, $windowview);
                }
            }

            $parkings = array();
            $parkingGroumd = false;
            $parkingUndgr = false;
            $parkingMultiLvl = false;
            $parkingOpen = false;
            foreach ($advertisement->{'parking-type'} as $parkingType) {
                if (in_array($parkingType, $parkings)) {
                    switch ($parkingType) {
                        case 'наземная':
                            $parkingGroumd = true;
                            break;
                        case 'подземная':
                            $parkingUndgr = true;
                            break;
                        case 'многоуровневая':
                            $parkingMultiLvl = true;
                            break;
                        case 'открытая':
                            $parkingOpen = true;
                            break;
                    }
                    array_push($parkings, $parkingType);
                }
            }

            $images = array();
            foreach($advertisement->image as $image) {
                $imageRow = [
                    'location_type' => 'remote',
                    'url' => (string)$image,
                ];
                array_push($images, $imageRow);
            }

            $advertisements[$advertisementId] = [
                'external_id' => $advertisementId,
                'deal_type_string' => !empty($advertisement->type) ? (string)$advertisement->type : '',
                'deal_type' => $this->findDealTypeCode($advertisement->type),
                'property_type_string' => !empty($advertisement->{'property-type'}) ? (string)$advertisement->{'property-type'} : '',
                'category' => !empty($advertisement->category) ? (string)$advertisement->category : '',
                'creation_date' => !empty($advertisement->{'creation-date'}) ? $this->formatDateString($advertisement->{'creation-date'}) : null ,
                'last_update_date' => !empty($advertisement->{'last-update-date'}) ? $this->formatDateString($advertisement->{'last-update-date'}) : null,
                'price' => !empty($advertisement->price->value) ? (float)$advertisement->price->value : 0,
                'price_unit' => !empty($advertisement->price->value) && !empty($advertisement->area->value) ? (float)$advertisement->price->value / (float)$advertisement->area->value : 0,
                'mortgage' => !empty($advertisement->mortgage) ? (int)$advertisement->mortgage : 0,
                'payed_adv' => !empty($advertisement->{'payed-adv'}) ? (int)$advertisement->{'payed-adv'} : 0,
                'utilities_included' => !empty($advertisement->{'utilities-included'}) ? (int)$advertisement->{'utilities-included'} : 0,
                'rooms' => !empty($advertisement->rooms) ? (int)$advertisement->rooms : '',
                'balcony' => !empty($advertisement->balcony) ? (string)$advertisement->balcony : '',
                'balcony_amount' => $balcony_amount,
                'loggia_amount' => $loggia_amount,
                'windowview_street' => $viewStreet,
                'windowview_yard' => $viewYard,
                'windowview_info' => count($windowviews) > 0 ? implode(', ', $windowviews) : '',
                'is_studio' => !empty($advertisement->studio) ? (int)$advertisement->studio : 0,
                'bathroom_unit' => !empty($advertisement->{'bathroom-unit'}) ? (string)$advertisement->{'bathroom-unit'} : '',
                'floor' => !empty($advertisement->floor) ? (int)$advertisement->floor : '',
                'total_floors' => !empty($advertisement->{'floors-total'}) ? (int)$advertisement->{'floors-total'} : '',
                'material' => !empty($advertisement->{'building-type'}) ? (string)$advertisement->{'building-type'} : '',
                'renovation' => !empty($advertisement->renovation) ? (string)$advertisement->renovation : '',
                'quality' => !empty($advertisement->quality) ? (string)$advertisement->quality : '',
                'phone' => !empty($advertisement->phone) ? (int)$advertisement->phone : 0,
                'internet' => !empty($advertisement->internet) ? (int)$advertisement->internet : 0,
                'rubbish_chute' => !empty($advertisement->{'rubbish-chute'}) ? (int)$advertisement->{'rubbish-chute'} : 0,
                'lift' => !empty($advertisement->lift) ? (int)$advertisement->lift : 0,
                'underground_parking' => $parkingUndgr,
                'ground_parking' => $parkingGroumd,
                'open_parking' => $parkingOpen,
                'multilevel_parking' => $parkingMultiLvl,
                'parking_info' => count($parkings) > 0 ? implode(', ', $parkings) : '',
                'description' => !empty($advertisement->description) ? (string)$advertisement->description : '',
                'building_type' => !empty($advertisement->{'building-type'}) ? (string)$advertisement->{'building-type'} : '',
                'building_series' => !empty($advertisement->{'building-series'}) ? (string)$advertisement->{'building-series'} : '',
                'ceiling_height' => !empty($advertisement->{'ceiling-height'}) ? (float)$advertisement->{'ceiling-height'} : '',
                'area' => !empty($advertisement->area->value) ? (float)$advertisement->area->value : '',
                'living_area' => !empty($advertisement->{'living-space'}->value) ? (float)$advertisement->{'living-space'}->value : '',
                'kitchen_area' => !empty($advertisement->{'kitchen-space'}->value) ? (float)$advertisement->{'kitchen-space'}->value : '',
                'deal_status' => !empty($advertisement->{'deal-status'}) ? (string)$advertisement->{'deal-status'} : '',
                'images' => $images,
                'street_type_id' => isset($streetTypeId) && !empty($streetTypeId) ? (int)$streetTypeId : null,
                'street_name' => isset($streetName) && !empty($streetName) ? (string)$streetName : '',
                'building_number' => isset($buildingNumber) && !empty($buildingNumber) ? (string)$buildingNumber : '',
                'location' => $location,
                'agent' => $agent,
            ];
		}

        return [
            'advertisements' => $advertisements,
        ];
    }

    /**
     * Format datetime string from feed for database 
     */
    private function formatDateString ($rawDate)
    {
        if (empty($rawDate)) return '';
        
        $parts = explode('T', $rawDate);
        $time = explode('+', $parts[1]);

        return $parts[0].' '.$time[0];
    }

    /**
     * find out deal type digital code by deal name string
     */
    private function findDealTypeCode($dealName)
    {
        if (empty($dealName)) return 0;
        //if (strpos(mb_strtolower($dealName), 'продажа')) return 1;
        //if (strpos(mb_strtolower($dealName), 'аренда')) return 2;
        if ((mb_strtolower($dealName)) === 'продажа') return 1;
        if ((mb_strtolower($dealName)) === 'аренда') return 2;
    }
}
<?php

namespace app\components\import\secondary;

use app\components\exceptions\AppException;
use app\components\import\secondary\SecondaryImportServiceInterface;

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
        $advertisements = [];

        if (!isset($data->offer)) {
            throw new AppException("Нет данных об объявлениях");
        }

		// Parse advertisements
        foreach ($data->offer as $advertisement) {

            $advertisementId = (string)$advertisement['internal-id'];

            $location = [
                'country' => !empty($advertisement->location->country) ? (string)$advertisement->location->country : '',
                'region' => !empty($advertisement->location->region) ? (string)$advertisement->location->region : '',
                'locality_name' => !empty($advertisement->location->{'locality-name'}) ? (string)$advertisement->location->{'locality-name'} : '',
                'sub_locality_name' => !empty($advertisement->location->{'sub-locality-name'}) ? (string)$advertisement->location->{'sub-locality-name'} : '',
                'address' => !empty($advertisement->location->address) ? (string)$advertisement->location->address : '',
                'latitude' => !empty($advertisement->location->latitude) ? (float)$advertisement->location->latitude : '',
                'longitude' => !empty($advertisement->location->longitude) ? (float)$advertisement->location->longitude : '',
            ];

            $agentPhones = [];
            foreach($advertisement->{'sales-agent'}->phone as $phone) {
                array_push($agentPhones, (string)$phone);
            }

            $agent = [
                'phones' => $agentPhones,
                'email' => !empty($advertisement->{'sales-agent'}->email) ? (string)$advertisement->{'sales-agent'}->email : '',
                'name' => !empty($advertisement->{'sales-agent'}->name) ? (string)$advertisement->{'sales-agent'}->name : '',
                'photo' => !empty($advertisement->{'sales-agent'}->photo) ? (string)$advertisement->{'sales-agent'}->photo : '',
            ];

            $images = [];
            foreach($advertisement->image as $image) {
                array_push($images, (string)$image);
            }

            $advertisements[$advertisementId] = [
                'deal_type_string' => !empty($advertisement->type) ? (string)$advertisement->type : '',
                'property_type_string' => !empty($advertisement->{'property-type'}) ? (string)$advertisement->{'property-type'} : '',
                'category' => !empty($advertisement->category) ? (string)$advertisement->category : '',
                'creation_date' => !empty($advertisement->{'creation-date'}) ? $advertisement->{'creation-date'} : null ,
                'last_update_date' => !empty($advertisement->{'last-update-date'}) ? $advertisement->{'last-update-date'} : null,
                'price' => !empty($advertisement->price->value) ? (float)$advertisement->price->value : 0,
                'mortgage' => !empty($advertisement->mortgage) ? (int)$advertisement->mortgage : 0,
                'payed_adv' => !empty($advertisement->{'payed-adv'}) ? (int)$advertisement->{'payed-adv'} : 0,
                'utilities_included' => !empty($advertisement->{'utilities-included'}) ? (int)$advertisement->{'utilities-included'} : 0,
                'rooms' => !empty($advertisement->rooms) ? (int)$advertisement->rooms : '',
                'balcony' => !empty($advertisement->balcony) ? (string)$advertisement->balcony : '',
                'bathroom_unit' => !empty($advertisement->{'bathroom-unit'}) ? (string)$advertisement->{'bathroom-unit'} : '',
                'floor' => !empty($advertisement->floor) ? (int)$advertisement->floor : '',
                'total_floors' => !empty($advertisement->{'floors-total'}) ? (int)$advertisement->{'floors-total'} : '',
                'renovation' => !empty($advertisement->renovation) ? (string)$advertisement->renovation : '',
                'quality' => !empty($advertisement->quality) ? (string)$advertisement->quality : '',
                'phone' => !empty($advertisement->phone) ? (int)$advertisement->phone : 0,
                'internet' => !empty($advertisement->internet) ? (int)$advertisement->internet : 0,
                'rubbish_chute' => !empty($advertisement->{'rubbish-chute'}) ? (int)$advertisement->{'rubbish-chute'} : 0,
                'lift' => !empty($advertisement->lift) ? (int)$advertisement->lift : 0,
                'description' => !empty($advertisement->description) ? (string)$advertisement->description : '',
                'building_type' => !empty($advertisement->{'building-type'}) ? (string)$advertisement->{'building-type'} : '',
                'building_series' => !empty($advertisement->{'building-series'}) ? (string)$advertisement->{'building-series'} : '',
                'ceiling_height' => !empty($advertisement->{'ceiling-height'}) ? (float)$advertisement->{'ceiling-height'} : '',
                'area' => !empty($advertisement->area->value) ? (float)$advertisement->area->value : '',
                'living_area' => !empty($advertisement->{'living-space'}->value) ? (float)$advertisement->{'living-space'}->value : '',
                'kitchen_area' => !empty($advertisement->{'kitchen-space'}->value) ? (float)$advertisement->{'kitchen-space'}->value : '',
                'deal_status' => !empty($advertisement->{'deal-status'}) ? (string)$advertisement->{'deal-status'} : '',
                'images' => $images,
                'location' => $location,
                'agent' => $agent,
            ];
		}

        return [
            'advertisements' => $advertisements,
        ];
    }
}
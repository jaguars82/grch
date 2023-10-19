<?php

namespace app\models\service;

use app\components\exceptions\AppException;
use app\models\SecondaryAdvertisement;
use app\models\SecondaryRoom;
use app\models\SecondaryCategory;
use app\models\SecondaryPropertyType;
use app\models\SecondaryBuildingSeries;
use app\models\SecondaryRenovation;
use app\models\SecondaryRoomImage;
use app\models\SecondaryImport;
use app\models\BuildingMaterial;
use app\models\Region;
use app\models\RegionDistrict;
use app\models\City;
use app\models\District;
use app\models\User;
use yii\httpclient\Client;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for processing agencies.
 */
class Agency extends \app\models\Agency
{
    /**
     * Count of updated advertisements
     *
     * @var int
     */
    public $updatedAdvertisementsCount = 0;

    /**
     * Count of inserted advertisements
     *
     * @var int
     */
    public $insertedAdvertisementsCount = 0;

    /**
     * Count of deactivated advertisements
     *
     * @var int
     */
    public $deactivatedAdvertisementsCount = 0;

    /**
     * Import advertisements data from array getting from parsed sources
     *
     * @param array $data array with flat's data
     * @throws \Exception
     */
    public function import($data = NULL)
    {
        if (is_null($data)) {
            return;
        }

        // var_dump($data);
        /*$values = [];
        foreach($data['advertisements'] as $adv) {
            if (!in_array($adv['parking_type'], $values)) {
                array_push($values, $adv['parking_type']);
            }
        }

        foreach ($values as $value) {
            echo $value; echo PHP_EOL;
        }*/

        $transaction = \Yii::$app->db->beginTransaction();

        try {
            if (empty($data['advertisements'])) {
                throw new AppException('Отсутсвуют данные об объявлениях');
            }

            if (!is_null($this->import)) {
                $this->import->touch('imported_at');
            }

            $advertisements = $this->insertOrUpdateAdvertisements($data['advertisements']);

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
        
    }

    /**
     * Insert new advertisements and update changed ones
     *
     * @param array $newbuildingComplexesData newbuilding complex data
     * @return SecondaryAdvertisement
     */
    private function insertOrUpdateAdvertisements($advertisementsData)
    {
        $advertisements = [];
        $savedAdvertisements = $this->getSecondaryAdvertisements()->indexBy('external_id')->all();

        $externalIdiesFromFeed = array();

        foreach($advertisementsData as $key => $advertisementData) {

            array_push($externalIdiesFromFeed, $key);
            
            if (array_key_exists($key, $savedAdvertisements)) {
                $advertisement = $savedAdvertisements[$advertisementData['external_id']];

                // update secondary rooms
                foreach ($advertisement->secondaryRooms as $secondaryRoom) {

                    // TODO
                    // update secondary rooms data here

                    // sync room images
                    $savedImagesURLs = array();
                    $newImagesURLs = array();
                    foreach ($secondaryRoom->images as $savedImage) {
                        array_push($savedImagesURLs, $savedImage->url);
                    }
                    
                    if (count($advertisementData['images']) > 0) {
                        foreach ($advertisementData['images'] as $image) {
                            array_push($newImagesURLs, $image['url']);
                            // add a new image to DB
                            if (!in_array($image['url'], $savedImagesURLs)) {
                                $image['secondary_room_id'] = $secondaryRoom->id;
                                $secondaryRoomImage = new SecondaryRoomImage($image);     
                                $secondaryRoomImage->save();
                            }
                        }
                    }

                    // delete not actual images (if there are any)
                    $toDeleteImgURLs = array_diff($savedImagesURLs, $newImagesURLs);
                    foreach ($toDeleteImgURLs as $deleteImgURL) {
                        $deleteImg = SecondaryRoomImage::findOne(['url' => $deleteImgURL]);
                        $deleteImg->delete();
                    }
                }
                //echo $key; echo PHP_EOL;
            } else {
                $newAdvertisementRow = array();

                // try to find author
                $authorId = 0;
                // by email
                if (!empty($advertisementData['agent']['email'])) {
                    $authorByMailTry = User::findByEmailAndAgency($advertisementData['agent']['email'], $this->id);
                    if (!empty($authorByMailTry)) {
                        $authorId = $authorByMailTry->id;
                    }
                }
                // by phone
                if ($authorId === 0 && count($advertisementData['agent']['phones']) > 0) {
                    foreach ($advertisementData['agent']['phones'] as $phone) {
                        $authorByPhoneTry = User::findByPhoneAndAgency($phone, $this->id);
                        if (!empty($authorByPhoneTry)) {
                            $authorId = $authorByPhoneTry->id;
                        }
                    }
                }

                $newAdvertisementRow['external_id'] = $advertisementData['external_id'];
                $newAdvertisementRow['deal_type'] = $advertisementData['deal_type'];
                $newAdvertisementRow['deal_status_string'] = $advertisementData['deal_status'];
                $newAdvertisementRow['agency_id'] = $this->id;
                $newAdvertisementRow['creation_type'] = 1;
                $newAdvertisementRow['author_id'] = $authorId !== 0 ? $authorId : null;
                $newAdvertisementRow['author_info'] = json_encode($advertisementData['agent']) ;
                $newAdvertisementRow['is_active'] = 1;
                $newAdvertisementRow['creation_date'] = $advertisementData['creation_date'];
                $newAdvertisementRow['last_update_date'] = $advertisementData['last_update_date'];

                // $encoding = mb_detect_encoding($newAdvertisementRow['deal_status_string']);
                // var_dump($newAdvertisementRow); die;

                $advertisement = new SecondaryAdvertisement($newAdvertisementRow);
                $advertisement->agency_id = $this->id;
                $advertisement->save();

                // try to figure out category ID
                $categoryId = 0;
                $categoryByNameTry = '';
                if (!empty($advertisementData['category'])) {
                    $categoryByNameTry = SecondaryCategory::getCategoryByName($advertisementData['category']);
                    if (!empty($categoryByNameTry)) {
                        $categoryId = $categoryByNameTry->id;
                    }
                }

                // try to figure out property type ID
                $propertyID = 0;
                $propertyByNameTry = '';
                if (!empty($advertisementData['property_type_string'])) {
                    $propertyByNameTry = SecondaryPropertyType::getPropertyTypeByName($advertisementData['property_type_string']);
                    if (!empty($propertyByNameTry)) {
                        $propertyID = $propertyByNameTry->id;
                    }
                }

                // try to figure out building series ID
                $seriesID = 0;
                $seriesByNameTry = '';
                if (!empty($advertisementData['building_series'])) {
                    $seriesByNameTry = SecondaryBuildingSeries::getBuildingSeriesByName($advertisementData['building_series']);
                    if (!empty($seriesByNameTry)) {
                        $seriesID = $seriesByNameTry->id;
                    }
                }

                // try to figure out renovation ID
                $renovationID = 0;
                $renovationByNameTry = '';
                if (!empty($advertisementData['renovation'])) {
                    $renovationByNameTry = SecondaryRenovation::getRenovationByName($advertisementData['renovation']);
                    if (!empty($renovationByNameTry)) {
                        $renovationID = $renovationByNameTry->id;
                    }
                }

                // try to figure out material ID
                $materialID = 0;
                $materialByNameTry = '';
                if (!empty($advertisementData['material'])) {
                    $materialByNameTry = BuildingMaterial::getMaterialByName($advertisementData['material']);
                    if (!empty($materialByNameTry)) {
                        $materialID = $materialByNameTry->id;
                    }
                }

                // try to figure out region ID
                $regionID = 0;
                $regionByNameTry = '';
                if (!empty($advertisementData['location']['region'])) {
                    $regionByNameTry = Region::getRegionByName($advertisementData['location']['region']);
                    if (!empty($regionByNameTry)) {
                        $regionID = $regionByNameTry->id;
                    }
                }

                // try to figure out region district ID
                $regionDistrictID = 0;
                $regionNameParts = [];
                $districtTry = '';
                if (!empty($advertisementData['location']['district'])) {
                    $regionNameParts = explode(' ', $advertisementData['location']['district']);
                }
                if (!empty($regionNameParts[0]) && !empty($regionID)) {
                    $districtTry = RegionDistrict::getRegionDistrictByRegionAndName($regionID, $regionNameParts[0]);
                    if (!empty($districtTry)) {
                        $regionDistrictID = $districtTry->id;
                    }
                }

                // try to figure out city ID
                $cityId = 0;
                $cityName = '';
                if (!empty($advertisementData['location']['locality_name'])) {
                    $cityTypes = ['город', 'городской округ', 'деревня', 'посёлок', 'коттеджный посёлок', 'рабочий посёлок', 'поселок', 'село', 'хутор'];
                    $cityName = $advertisementData['location']['locality_name'];
                    foreach ($cityTypes as $cityType) {
                        $cityName = str_replace(' '.$cityType, '', $cityName);
                    }
                }
                if (!empty($cityName) && !empty($regionID)) {
                    $cityTry = City::getCityByRegionAndName($regionID, $cityName);
                    if (!empty($cityTry)) {
                        $cityId = $cityTry->id;
                    }
                }

                // try to figure out district of a city ID
                $districtId = 0;
                $districtName = '';
                $cityDistrictTry = '';
                if (!empty($advertisementData['location']['sub_locality_name'])) {
                    $districtName = !empty($advertisementData['location']['non_admin_sub_locality_name']) ? $advertisementData['location']['non_admin_sub_locality_name'] : $advertisementData['location']['sub_locality_name'];
                    $districtTypes = ['микрорайон'];
                    foreach ($districtTypes as $districtType) {
                        $districtName = str_replace(' '.$districtType, '', $districtName);
                    }
                }
                if (!empty($districtName) && !empty($cityId)) {
                    $cityDistrictTry = District::getDistrictByCityAndName($cityId, $districtName);
                    if (!empty($cityDistrictTry)) {
                        $districtId = $cityDistrictTry->id;
                    }
                }

                // try to figure out bathroom unit index
                $bathroomIndex = array_search($advertisementData['bathroom_unit'], SecondaryRoom::$bathroom);

                // try to figure out quality index
                $qualityIndex = array_search($advertisementData['quality'], SecondaryRoom::$quality);

                $newSecondaryRoomRow = array();

                $newSecondaryRoomRow['advertisement_id'] = $advertisement->id;
                $newSecondaryRoomRow['category_id'] = $categoryId !== 0 ? $categoryId : null;
                $newSecondaryRoomRow['category_string'] = $categoryId === 0 && !empty($advertisementData['category']) ? $advertisementData['category'] : '';
                $newSecondaryRoomRow['property_type_id'] = $propertyID !== 0 ? $propertyID : null;
                $newSecondaryRoomRow['property_type_string'] = $propertyID === 0 && !empty($advertisementData['category']) ? $advertisementData['category'] : '';
                $newSecondaryRoomRow['building_series_id'] = $seriesID !== 0 ? $seriesID : null;
                $newSecondaryRoomRow['building_series_string'] = $seriesID === 0 && !empty($advertisementData['building_series']) ? $advertisementData['building_series'] : '';
                $newSecondaryRoomRow['price'] = $advertisementData['price'];
                $newSecondaryRoomRow['unit_price'] = $advertisementData['price_unit'];
                $newSecondaryRoomRow['mortgage'] = $advertisementData['mortgage'];
                $newSecondaryRoomRow['detail'] = $advertisementData['description'];
                $newSecondaryRoomRow['area'] = $advertisementData['area'];
                $newSecondaryRoomRow['kitchen_area'] = $advertisementData['kitchen_area'];
                $newSecondaryRoomRow['living_area'] = $advertisementData['living_area'];
                $newSecondaryRoomRow['ceiling_height'] = $advertisementData['ceiling_height'];
                $newSecondaryRoomRow['rooms'] = $advertisementData['rooms'];
                $newSecondaryRoomRow['balcony_info'] = empty($balcony_amount) && empty($loggia_amount) ? $advertisementData['balcony'] : '';
                $newSecondaryRoomRow['balcony_amount'] = $advertisementData['balcony_amount'];
                $newSecondaryRoomRow['loggia_amount'] = $advertisementData['loggia_amount'];
                $newSecondaryRoomRow['windowview_info'] = $advertisementData['windowview_info'];
                $newSecondaryRoomRow['windowview_street'] = $advertisementData['windowview_street'];
                $newSecondaryRoomRow['windowview_yard'] = $advertisementData['windowview_yard'];
                $newSecondaryRoomRow['is_studio'] = $advertisementData['is_studio'];
                $newSecondaryRoomRow['renovation_id'] = $renovationID !== 0 ? $renovationID : null;
                $newSecondaryRoomRow['renovation_string'] = $renovationID === 0 && !empty($advertisementData['renovation']) ? $advertisementData['renovation'] : '';
                $newSecondaryRoomRow['quality_index'] = $qualityIndex === false ? null : $qualityIndex;
                $newSecondaryRoomRow['quality_string'] = $qualityIndex === false ? $advertisementData['quality'] : '';
                $newSecondaryRoomRow['floor'] = $advertisementData['floor'];
                $newSecondaryRoomRow['total_floors'] = $advertisementData['total_floors'];
                $newSecondaryRoomRow['material_id'] = $materialID !== 0 ? $materialID : null;
                $newSecondaryRoomRow['material'] = $materialID === 0 && !empty($advertisementData['material']) ? $advertisementData['material'] : '';
                $newSecondaryRoomRow['elevator'] = $advertisementData['lift'];
                $newSecondaryRoomRow['rubbish_chute'] = $advertisementData['rubbish_chute'];
                $newSecondaryRoomRow['phone_line'] = $advertisementData['phone'];
                $newSecondaryRoomRow['internet'] = $advertisementData['internet'];
                $newSecondaryRoomRow['bathroom_index'] = $bathroomIndex === false ? null : $bathroomIndex;
                $newSecondaryRoomRow['bathroom_unit'] = $bathroomIndex === false ? $advertisementData['bathroom_unit'] : '';
                $newSecondaryRoomRow['underground_parking'] = $advertisementData['underground_parking'];
                $newSecondaryRoomRow['ground_parking'] = $advertisementData['ground_parking'];
                $newSecondaryRoomRow['open_parking'] = $advertisementData['open_parking'];
                $newSecondaryRoomRow['multilevel_parking'] = $advertisementData['multilevel_parking'];
                $newSecondaryRoomRow['parking_info'] = $advertisementData['parking_info'];
                $newSecondaryRoomRow['longitude'] = $advertisementData['location']['longitude'];
                $newSecondaryRoomRow['latitude'] = $advertisementData['location']['latitude'];
                $newSecondaryRoomRow['region_id'] = $regionID !== 0 ? $regionID : null;
                $newSecondaryRoomRow['region_district_id'] = $regionDistrictID !== 0 ? $regionDistrictID : null;
                $newSecondaryRoomRow['city_id'] = $cityId !== 0 ? $cityId : null;
                $newSecondaryRoomRow['district_id'] = $districtId !== 0 ? $districtId : null;
                $newSecondaryRoomRow['street_type_id'] = $advertisementData['street_type_id'];
                $newSecondaryRoomRow['street_name'] = $advertisementData['street_name'];
                $newSecondaryRoomRow['building_number'] = $advertisementData['building_number'];
                $newSecondaryRoomRow['address'] = $advertisementData['location']['address'];
                $newSecondaryRoomRow['location_info'] = json_encode($advertisementData['location']);

                // var_dump($newSecondaryRoomRow); die;

                $secondaryRoom = new SecondaryRoom($newSecondaryRoomRow);
                $secondaryRoom->save();

                // room images
                if (count($advertisementData['images']) > 0) {
                    foreach ($advertisementData['images'] as $image) {
                        $image['secondary_room_id'] = $secondaryRoom->id;
                        $secondaryRoomImage = new SecondaryRoomImage($image);     
                        $secondaryRoomImage->save();                     
                    }
                }

                //$newAdvertisementRow['']
                $this->insertedAdvertisementsCount++;
            }

            $advertisements[$key] = $advertisement;
        }

        // remove advertisements from DB if they had been removed from feed
        $savedAdvIdies = array_keys($savedAdvertisements);
        $adsToDeleteIdies = array_diff($savedAdvIdies, $externalIdiesFromFeed);

        foreach ($adsToDeleteIdies as $deleteId) {
            $advertisementToDelete = SecondaryAdvertisement::findOne(['external_id' => $deleteId, 'creation_type' => 1, 'agency_id' => $this->id]);
            if ($advertisementToDelete !== null) {
                foreach ($advertisementToDelete->statusLabels as $label) {
                    $advertisementToDelete->unlink('statusLabels', $label, true);
                }
                foreach ($advertisementToDelete->secondaryRooms as $room) {
                    foreach ($room->images as $image) {
                        $image->delete();
                    }
                    $room->delete();
                }
                $advertisementToDelete->delete();
            }
        }

        return $advertisements;
    }
}

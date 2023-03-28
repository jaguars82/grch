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

        foreach($advertisementsData as $key => $advertisementData) {
            
            if (array_key_exists($key, $savedAdvertisements)) {
                $advertisement = $savedAdvertisements[$advertisementData['external_id']];
                echo $key; echo PHP_EOL;
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
                if (!empty($advertisementData['category'])) {
                    $categoryByNameTry = SecondaryCategory::getCategoryByName($advertisementData['category']);
                    if (!empty($categoryByNameTry)) {
                        $categoryId = $categoryByNameTry->id;
                    }
                }

                // try to figure out property type ID
                $propertyID = 0;
                if (!empty($advertisementData['property_type_string'])) {
                    $propertyByNameTry = SecondaryPropertyType::getPropertyTypeByName($advertisementData['property_type_string']);
                    if (!empty($propertyByNameTry)) {
                        $propertyID = $propertyByNameTry->id;
                    }
                }

                // try to figure out building series ID
                $seriesID = 0;
                if (!empty($advertisementData['building_series'])) {
                    $seriesByNameTry = SecondaryBuildingSeries::getBuildingSeriesByName($advertisementData['building_series']);
                    if (!empty($seriesByNameTry)) {
                        $seriesID = $seriesByNameTry->id;
                    }
                }

                // try to figure out renovation ID
                $renovationID = 0;
                if (!empty($advertisementData['building_series'])) {
                    $renovationByNameTry = SecondaryRenovation::getRenovationByName($advertisementData['renovation']);
                    if (!empty($renovationByNameTry)) {
                        $renovationID = $renovationByNameTry->id;
                    }
                }

                // try to figure out material ID
                $materialID = 0;
                if (!empty($advertisementData['material'])) {
                    $materialByNameTry = BuildingMaterial::getMaterialByName($advertisementData['material']);
                    if (!empty($materialByNameTry)) {
                        $materialID = $materialByNameTry->id;
                    }
                }

                // try to figure out region ID
                $regionID = 0;
                if (!empty($advertisementData['location']['region'])) {
                    $regionByNameTry = Region::getRegionByName($advertisementData['location']['region']);
                    if (!empty($regionByNameTry)) {
                        $regionID = $regionByNameTry->id;
                    }
                }

                // try to figure out region district ID
                $regionDistrictID = 0;
                if (!empty($advertisementData['location']['district'])) {
                    $regionNameParts = explode(' ', $advertisementData['location']['district']);
                }
                if (isset($regionNameParts) && !empty($regionNameParts[0]) && !empty($regionID)) {
                    $districtTry = RegionDistrict::getRegionDistrictByRegionAndName($regionID, $regionNameParts[0]);
                    if (!empty($districtTry)) {
                        $regionDistrictID = $districtTry->id;
                    }
                }

                // try to figure out city ID
                $cityId = 0;
                if (!empty($advertisementData['location']['locality_name'])) {
                    $cityTypes = ['город', 'деревня', 'посёлок', 'поселок', 'село', 'хутор'];
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

        return $advertisements;
    }

    /**
     * Insert new newbuildings and update changed newbuildings
     *
     * @param array $newbuildingComplexes newbuilding complexes array
     * @param array $newbuildingsData newbuildings data
     * @return Newbuilding
     */
    private function insertOrUpdateNewbuilding($newbuildingComplexes, $newbuildingsData)
    {
        $newbuildings = [];

        $savedNewbuildings = [];
        foreach($newbuildingComplexes as $key => $newbuildingComplex) {
            $savedNewbuildings[$key] = $newbuildingComplex->getNewbuildings()->indexBy('feed_name')->all();
        }

        foreach($newbuildingsData as $key => $newbuildingData) {
            $objectId = $newbuildingData['objectId'];
            unset($newbuildingData['objectId']);
            $newbuildingComplex = $newbuildingComplexes[$objectId];

            if (array_key_exists($newbuildingData['name'], $savedNewbuildings[$objectId])) {
                $newbuilding = $savedNewbuildings[$objectId][$newbuildingData['name']];
                // the following commented code was responsible for updating newbuilding data (deadline, material etc.)
                /*if (isset($newbuildingData['deadline']) && $newbuildingData['deadline'] != $newbuilding->deadline) {
                    $newbuildingData['name'] = $savedNewbuildings[$objectId][$newbuildingData['name']]['name']; // this prevents raplacement of 'name' in database with 'name' from feed
                    $newbuilding->fill($newbuildingData);
                    $newbuilding->save();
                    $this->updatedNewbuildingsCount++;
                }*/
            } else {
                $newbuildingData['feed_name'] = $newbuildingData['name'];

                $newbuilding = new Newbuilding($newbuildingData);
                $newbuilding->link('newbuildingComplex', $newbuildingComplex);
                $this->insertedNewbuildingsCount++;
            }

            $newbuildings[$key] = $newbuilding;
        }

        return $newbuildings;
    }

    /**
     * Insert new floor layouts
     *
     * @param array $newbuildings newbuildings array
     * @param array $floorLayoutsData floor layouts data
     */
    private function insertFloorLayouts($newbuildings, $floorLayoutsData)
    {
        $compositeFlats = [];

        $savedFlats = [];
        foreach($newbuildings as $key => $newbuilding) {
            $savedFloorLayouts[$key] = $newbuilding->getFloorLayouts()->all();
        }

        foreach($floorLayoutsData as $floorLayoutData) {
            $houseId = $floorLayoutData['houseId'];
            unset($floorLayoutData['houseId']);

            if (!($floorLayout = $this->checkFloorLayoutExists($savedFloorLayouts[$houseId], $floorLayoutData))) {
                $floorLayout = new FloorLayout($floorLayoutData);
                $floorLayout->newbuilding_id = $newbuildings[$houseId]->id;
                $floorLayout->image = $this->downloadFile($floorLayoutData['image']);
                $floorLayout->save(false);
            }
        }
    }

    /**
     * Insert new flats and update changed flats
     *
     * @param array $newbuildings newbuildings array
     * @param array $flatsData flats data
     */
    private function insertOrUpdateFlats($newbuildings, $flatsData)
    {
        $compositeFlats = [];

        $savedFlats = [];
		$savedFlatIds = [];
        foreach($newbuildings as $key => $newbuilding) {
            $savedFlats[$key] = $newbuilding->getFlats()->all();

			$current = ArrayHelper::getColumn($savedFlats[$key], 'id');
			$savedFlatIds = array_merge($savedFlatIds, $current);
        }

		$actualFlatIds = [];

        foreach($flatsData as $flatData) {
            $houseId = $flatData['houseId'];
            unset($flatData['houseId']);

            if (isset($flatData['compositeFlatId'])) {
                $compositeFlatId = $flatData['compositeFlatId'];
                unset($flatData['compositeFlatId']);
            } else {
                $compositeFlatId = NULL;
            }

            if (($flat = $this->checkFlatExists($savedFlats[$houseId], $flatData))) {
                if ((float)$flatData['unit_price_cash'] !== (float)$flat->unit_price_cash
                    || (float)$flatData['price_cash'] !== (float)$flat->price_cash
                    || (isset($flatData['unit_price_credit']) && (float)$flatData['unit_price_credit'] !== (float)$flat->unit_price_credit)
                    || (isset($flatData['price_credit']) && (float)$flatData['price_credit'] !== (float)$flat->price_credit)
                    || $flatData['status'] != $flat->status
                ) {

                    $status = $flat->status;
                    $flat->fill($flatData, ['section', 'rooms']);
                    //$flat->rooms = $flatData['rooms'];
                    $flat = \app\models\service\Flat::applyFlatPostionOnFloorLayout($flat, ['status' => $flatData['status']], $status);
                    $flat->save(false);
                    $this->updatedFlatsCount++;
                }
				else {
					$flat->touch('updated_at');
				}

				$actualFlatIds[] = $flat->id;
            } else {
                $flat = new Flat($flatData);
                $flat->newbuilding_id = $newbuildings[$houseId]->id;
                $flat->rooms = $flatData['rooms'];

                if (!is_null($compositeFlatId)) {
                    if (isset($compositeFlats[$compositeFlatId])) {
                        $compositeFlat = $compositeFlats[$compositeFlatId];
                    } else {
                        $compositeFlat = new CompositeFlat();
                        $compositeFlat->save();
                        $compositeFlats[$compositeFlatId] = $compositeFlat;
                    }

                    $flat->composite_flat_id = $compositeFlat->id;
                }

                if (isset($flatData['layout'])) {
                    $flat->layout = $this->downloadFile($flatData['layout'], $flat);
                }

                $flat->save(false);
                $this->insertedFlatsCount++;
            }

		}

		$missedFlats = array_diff($savedFlatIds, $actualFlatIds);

		$this->updateFlatStatus($missedFlats, Flat::STATUS_SOLD);

    }

	private function updateFlatStatus($flatIds, $status)
	{
		$flats = Flat::find()->where(['in', 'id', $flatIds])->all();

		foreach ($flats as $flat) {
			$flat->status = $status;
			$flat->save();
		}
	}

    /**
     * Check that floor layout with given attributes exists
     *
     * @param array $floorLayouts
     * @param array $floorLayoutData
     * @return boolean
     */
    private function checkFloorLayoutExists($floorLayouts, $floorLayoutData)
    {
        foreach($floorLayouts as $floorLayout) {
            if ($floorLayout->floor == $floorLayoutData['floor']
                && $floorLayout->section == $floorLayoutData['section']
            ) {
                return $floorLayout;
            }
        }

        return false;
    }

    /**
     * Check that flat with given attributes exists
     *
     * @param array $flats
     * @param array $flatData
     * @return boolean
     */
    private function checkFlatExists($flats, $flatData)
    {
        foreach($flats as $flat) {
            if ($flat->number == $flatData['number']
                && $flat->floor == $flatData['floor']
            ) {
                return $flat;
            }
        }

        return false;
    }

     /** Download file by url
     *
     * @param type $url url where file is enable
     * @return string saved filename (only basename)
     */
    private function downloadFile($url, $flat = null)
    {
        if (is_null($url)) {
            return NULL;
        }

        $filename = mt_rand() . '-' . basename($url);
		$url = str_replace ( ' ', '%20', $url);
		$fh = fopen(\Yii::getAlias("@webroot/uploads/$filename"), 'w');
        $client = new Client([
            'transport' => 'yii\httpclient\CurlTransport'
        ]);
		try {
			$response = $client->createRequest()
	            ->setMethod('GET')
	            ->setUrl($url)
	            ->setOutputFile($fh)
	            ->send();
		} catch (\Exception $e) {
			return NULL;
		}

        return $filename;
    }
}

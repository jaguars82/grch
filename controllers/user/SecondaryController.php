<?php

namespace app\controllers\user;

use app\components\traits\CustomRedirects;
use app\components\SharedDataFilter;
use app\models\SecondaryAdvertisement;
use app\models\SecondaryCategory;
use app\models\SecondaryRoom;
use app\models\SecondaryRoomFee;
use app\models\SecondaryRoomImage;
use app\models\form\SecondaryAdvertisementForm;
use app\models\form\SecondaryRoomForm;
use app\models\SecondaryRenovation;
use app\models\SecondaryBuildingSeries;
use app\models\BuildingMaterial;
use app\models\Developer;
use app\models\Agency;
use app\models\NewbuildingComplex;
use app\models\Newbuilding;
use app\models\Entrance;
use app\models\Flat;
use app\models\Region;
use app\models\RegionDistrict;
use app\models\City;
use app\models\District;
use app\models\StreetType;
use app\models\StatusLabel;
use app\models\StatusLabelType;
use app\models\User;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use tebe\inertia\web\Controller;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\data\Pagination;
use \Datetime;

class SecondaryController extends Controller
{
    use CustomRedirects;

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'create' => ['GET', 'POST'],
                    'index' => ['GET', 'POST'],
                    'view' => ['GET', 'POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['create', 'index', 'view'],
                        'roles' => ['admin', 'manager', 'agent'],
                    ],
                ]
            ],
            [
                'class' => SharedDataFilter::class
            ],
        ];
    }


    public function actionCreate()
    {
        // create new advertisement
        if (\Yii::$app->request->isPost && \Yii::$app->request->post('operation') === 'create_add') {
            $advertisementForm = new SecondaryAdvertisementForm();
            $roomForm = new SecondaryRoomForm();

            $transaction = \Yii::$app->db->beginTransaction();

            try {
                $advertisementForm->load(\Yii::$app->request->post(), '');
                $now = (new DateTime())->format('Y-m-d H:i:s');
                $advertisementForm->creation_date = $now;
                $advertisementForm->last_update_date = $now;
                $advertisementModel = (new SecondaryAdvertisement())->fill($advertisementForm->attributes);
                //$advertisementModel->save();
                if (!$advertisementModel->save()) {
                    
                    throw new \Exception('Ошибка сохранения объявления');
                    
                    // TOFIX Delete (or comment) 4 lines below after testing
                    /*\Yii::error($advertisementModel->errors, 'save-advertisement');
                    //echo '<pre>'; var_dump(\Yii::$app->request->post()); echo '</pre>';
                    var_dump($advertisementModel->errors);
                    echo '<pre>'; var_dump($advertisementModel); echo '</pre>';
                    die;*/
                }

                $roomForm->load(\Yii::$app->request->post(), '');
                $roomForm->advertisement_id = $advertisementModel->id;
                $roomForm->process();
                $roomModel = (new SecondaryRoom())->fill($roomForm->attributes);
                
                if(!$roomModel->validate()){
                    throw new \Exception('Ошибка валидации объекта');
                   
                    // TOFIX delete (or comment) 4 lines below after testing
                    /*echo 'ошибки при валидации объявления (объекта)';
                    \Yii::error($roomModel->errors, 'validation');
                    echo '<pre>'; var_dump($roomModel->errors); echo '</pre>';
                    echo '<pre>'; var_dump($roomModel); echo '</pre>';
                    die;*/
                }

                if (!$roomModel->save()) {
                    throw new \Exception('Ошибка сохранения объекта продажи');
                    
                    // TOFIX Delete (or comment) 4 lines below after testing
                    /*\Yii::error($roomModel->errors, 'save-secondaryRoom');
                    var_dump($roomModel->errors);
                    echo '<pre>'; var_dump($roomModel); echo '</pre>';
                    die;*/
                }
                
                if (count($roomForm->images)) {
                    foreach ($roomForm->images as $image) {
                        $newImage = new SecondaryRoomImage();
                        $newImage->secondary_room_id = $roomModel->id;
                        $newImage->location_type = 'local';
                        $newImage->filename = $image;
                        $newImage->save();
                    }
                }
                
                $transaction->commit();

                // Set success flash-message
                \Yii::$app->session->setFlash('success', 'Объявление успешно создано!');

                return $this->redirect('/secondary/index');

            } catch (\Exception $e) {
                $transaction->rollBack();

                // Set error flash-message
                \Yii::$app->session->setFlash('error', $e->getMessage());

                return $this->redirectBackWhenException($e);
            }
        }
        
        return $this->inertia('User/Secondary/Create', [
            'user' => \Yii::$app->user->identity,
            'flash' => \Yii::$app->session->getAllFlashes(),
            'secondaryCategories' => SecondaryCategory::getCategoryTree(),
            'buildingMaterials' => BuildingMaterial::getMaterialList(),
            'renovations' => SecondaryRenovation::getRenovationList(),
            'buildingSeries' => SecondaryBuildingSeries::getBuildingSeriesList(),
            'bathroomUnit' => SecondaryRoom::$bathroom,
            'quality' => SecondaryRoom::$quality,
            'developers' => Developer::getAllAsList(),
            'buildingComplexes' => \Yii::$app->request->post('developerId') ? NewbuildingComplex::find()->where(['developer_id' => \Yii::$app->request->post('developerId')])->select(['id', 'name'])->asArray()->all() : [],
            'buildings' => \Yii::$app->request->post('complexId') ? Newbuilding::find()->where(['newbuilding_complex_id' => \Yii::$app->request->post('complexId')])->select(['id', 'name'])->asArray()->all() : [],
            'entrances' => \Yii::$app->request->post('buildingId') ? Entrance::find()->where(['newbuilding_id' => \Yii::$app->request->post('buildingId')])->select(['id', 'name'])->asArray()->all() : [],
            'flats' => \Yii::$app->request->post('entranceId') ? Flat::find()->where(['entrance_id' => \Yii::$app->request->post('entranceId')])->select(['id', 'number', 'floor'])->orderBy(['number' => SORT_ASC])->asArray()->all() : [],
            'regions' => Region::getAllAsList(),
            'regionDistricts' => \Yii::$app->request->post('regionId') ? RegionDistrict::find()->where(['region_id' => \Yii::$app->request->post('regionId')])->orderBy(['name' => SORT_ASC])->asArray()->all() : [],
            'cities' => (\Yii::$app->request->post('regionDistrictId') ? City::find()->where(['region_district_id' => \Yii::$app->request->post('regionDistrictId')])->orderBy(['name' => SORT_ASC])->asArray()->all() : \Yii::$app->request->post('regionId')) ? City::find()->where(['region_id' => \Yii::$app->request->post('regionId')])->orderBy(['name' => SORT_ASC])->asArray()->all() : [],
            'cityDistricts' => \Yii::$app->request->post('cityId') ? District::find()->where(['city_id' => \Yii::$app->request->post('cityId')])->orderBy(['name' => SORT_ASC])->asArray()->all() : [],
            'streetTypes' => StreetType::getAllAsList(),
            'streetList' => SecondaryRoom::getStreetList(),
        ]);
    }


    public function actionIndex()
    {
        if (\Yii::$app->user->identity->role === 'manager') {
            $agents = Agency::getUsersByAgency(\Yii::$app->user->identity->agency_id);
        }

        if (\Yii::$app->request->isPost) {
            // Assign filters for all post operations
            if (null !== \Yii::$app->request->post('agency') && !empty(\Yii::$app->request->post('agency'))) {
                $agents = Agency::getUsersByAgency(\Yii::$app->request->post('agency'));
            }
            $agencyFilter = \Yii::$app->request->post('agency') !== null && !empty(\Yii::$app->request->post('agency')) ? \Yii::$app->request->post('agency') : null;
            $agentFilter = \Yii::$app->request->post('agent') !== null && !empty(\Yii::$app->request->post('agent')) ? \Yii::$app->request->post('agent') : null;
            $categoryFilter = \Yii::$app->request->post('category') !== null && !empty(\Yii::$app->request->post('category')) ? \Yii::$app->request->post('category') : null;

            switch (\Yii::$app->request->post('operation')) {
                case 'setAgentFee':
                    try {
                        $transaction = \Yii::$app->db->beginTransaction();
                        $advertisement = SecondaryAdvertisement::findOne(\Yii::$app->request->post('secondary_advertisement_id'));
                        foreach ($advertisement->secondaryRooms as $room) {
                            $agentFee = new SecondaryRoomFee;
                            $agentFee->attributes = \Yii::$app->request->post();
                            $agentFee->secondary_room_id = $room->id;
                            $agentFee->created_user_id = \Yii::$app->user->identity->id;
                            if (empty($agentFee->fee_type)) {
                                $agentFee->fee_type = SecondaryRoomFee::TYPE_AMOUNT;
                            }
                            $agentFee->save();
                        }
                        $transaction->commit();
                    } catch (Exception $e) {
                        //return $this->redirectBackWhenException($e);
                    }
                    break;
                case 'setStatus': 
                    try {
                        $transaction = \Yii::$app->db->beginTransaction();
                        $advertisement = SecondaryAdvertisement::findOne(\Yii::$app->request->post('secondary_advertisement_id'));
                        $statusLabel = new StatusLabel;
                        $statusLabel->attributes = \Yii::$app->request->post();
                        $statusLabel->creator_id = \Yii::$app->user->identity->id;
                        $statusLabel->save();
                        $advertisement->link('statusLabels', $statusLabel);
                        $transaction->commit();

                        // Send a message to Telegram chat
                        $roomParentCategory = null; // We set a common SecondaryRomm parent category for all the secondary rooms (objects) of the current advertisement (for typically we have only one object within an advertisement)

                        // Prepare data for message
                        $advertisementData = [
                            'id' => $advertisement->id,
                            'deal_type' => SecondaryAdvertisement::$dealType[$advertisement->deal_type],
                            'created' => date('d.m.Y', strtotime($advertisement->creation_date)),
                        ];

                        // Adding information about the advertisement author
                        $author_name = null;
                        $author_phone = null;
                        $author_email = null;
                        
                        $author = !empty($advertisement->author_id) ? User::findOne($advertisement->author_id) : null;
                        
                        // If didn't found the author among registered users
                        if ($author === null) {
                            $authorInfo = json_decode($advertisement->author_info, true);
                            
                            $author_name = !empty($authorInfo['name']) ? $authorInfo['name'] : null;
                            
                            $author_email = !empty($authorInfo['email']) ? $authorInfo['email'] : null;

                            if (count($authorInfo['phones'])) {
                                $author_phone = implode(', ', $authorInfo['phones']);
                            }
                        } else { // if the author is in the user's base
                            $author_name = $author->first_name;
                            if (!empty($author->middle_name)) {
                                $author_name .= ' '.$author->middle_name;
                            }
                            if (!empty($author->last_name)) {
                                $author_name .= ' '.$author->last_name;
                            }

                            $author_email = $author->email;
                            $author_phone = !empty($author->phone) ? $author->phone : null;
                        }

                        $advertisementData['author_name'] = $author_name;
                        $advertisementData['author_email'] = $author_email;
                        $advertisementData['author_phone'] = $author_phone;

                        // Secondary rooms
                        foreach ($advertisement->secondaryRooms as $room) {
                            
                            if (!empty($room->category_id) && is_null($roomParentCategory)) {
                                $roomParentCategory = SecondaryCategory::$root_category_idies[$room->secondaryCategory->parent_id];
                            }
                            
                            $roomItem = [
                                'category_name' => $room->secondaryCategory ? $room->secondaryCategory->name : '',
                                'deal_code' => $advertisement->deal_type === SecondaryAdvertisement::DEAL_TYPE_SELL ? 'П' : 'А',
                                'rooms' => $room->rooms ? $room->rooms : '',
                                'area' => $room->area ? $room->area : '',
                                'price' => $room->price ? $room->price : '',
                                'floor' => $room->floor ? $room->floor : '',
                                'detail' => $room->detail ?  \Yii::$app->formatter->asPlainShortenText($room->detail) : '',
                                'address' => $room->address,
                                'location_info' => json_decode($room->location_info, true),
                            ];

                            $roomItem['agent_fee'] = ArrayHelper::toArray($room->agentFee);
                            
                            // Add information about room params from data base
                            $roomItem = $this->processRoomParameters($room, $roomItem);

                            // Get location params
                            // Region name and code
                            $regionName = null;
                            if (array_key_exists('region_DB', $roomItem) && !empty($roomItem['region_DB']['name'])) {
                                $regionName = $roomItem['region_DB']['name'];
                            } elseif (array_key_exists('region', $roomItem['location_info']) && !empty($roomItem['location_info']['region'])) {
                                $regionName = $roomItem['location_info']['region'];
                            }

                            $regionCode = \Yii::$app->locationHelper->findRegionKey($regionName) ?? null;

                            // District name and code
                            // First try to find out a district of a city
                            $districtName = null;
                            if (array_key_exists('district_DB', $roomItem) && !empty($roomItem['district_DB']['name'])) {
                                $districtName = $roomItem['district_DB']['name'];
                            } elseif (array_key_exists('sub_locality_name', $roomItem['location_info']) && !empty($roomItem['location_info']['sub_locality_name'])) {
                                $districtName = $roomItem['location_info']['sub_locality_name'];
                            }

                            // Then (if a tistrict of a city hasn't been tetected) try to find out a district of a region
                            if (is_null($districtName)) {
                                if (array_key_exists('region_district_DB', $roomItem) && !empty($roomItem['region_district_DB']['name'])) {
                                    $districtName = $roomItem['region_district_DB']['name'];
                                } elseif (array_key_exists('district', $roomItem['location_info']) && !empty($roomItem['location_info']['district'])) {
                                    $districtName = $roomItem['location_info']['district'];
                                }    
                            }

                            $districtCode = \Yii::$app->locationHelper->findDistrictCode($regionCode, $districtName) ?? null;

                            $roomItem['district_code'] = $districtCode;

                            // address string
                            $addressString = null;
                            if (!empty($roomItem['address'])) {
                                $addressString = $roomItem['address'];
                            } elseif (array_key_exists('address', $roomItem['location_info']) && !empty($roomItem['location_info']['address'])) {
                                $addressString = $roomItem['location_info']['address'];
                            }

                            $roomItem['address_string'] = $addressString;

                            $advertisementData['rooms'][] = $roomItem;
                        }
                        
                        $statusData = [
                            'name' => StatusLabelType::getNameById($statusLabel->label_type_id),
                            'has_expiration' => $statusLabel->has_expiration_date ? true : false,
                            'expires_at' => date('d.m.Y', strtotime($statusLabel->expires_at)),
                        ];

                        \Yii::$app->telegram->sendToAllGroups($advertisementData, $statusData, $advertisement->deal_type, $roomParentCategory);

                    } catch (Exception $e) {
                        //return $this->redirectBackWhenException($e);
                    }
                    break;
                case 'unsetAgentFee':
                    $agentFee = SecondaryRoomFee::findOne(\Yii::$app->request->post('fee_id'));
                    $agentFee->delete();
                    break;
                case 'unsetStatus':
                    $advertisement = SecondaryAdvertisement::findOne(\Yii::$app->request->post('secondary_advertisement_id'));
                    $statusLabel = StatusLabel::findOne(\Yii::$app->request->post('status_label_id'));
                    $advertisement->unlink('statusLabels', $statusLabel, true);
                    $statusLabel->delete();
                    break;
                case 'deleteAdd':
                    $transaction = \Yii::$app->db->beginTransaction();
                    $advertisement = SecondaryAdvertisement::findOne(\Yii::$app->request->post('id'));
                    foreach ($advertisement->statusLabels as $statusLabel) {
                        $advertisement->unlink('statusLabels', $statusLabel, true);
                    }
                    foreach ($advertisement->secondaryRooms as $secondaryRoom) {
                        foreach ($secondaryRoom->images as $image) {
                            $image->delete();
                        }
                        foreach ($secondaryRoom->agentFee as $fee) {
                            $fee->delete();
                        }
                        $secondaryRoom->delete();
                    }
                    $advertisement->delete();
                    $transaction->commit();
                    break;
            }
        }

        $query = SecondaryAdvertisement::find()
        //->where(['initiator_id' => \Yii::$app->user->id])
        ->where(['is_active' => 1]);

        if (\Yii::$app->user->identity->role === 'agent') {
            $query->andWhere(['author_id' => \Yii::$app->user->identity->id]);
        }

        if (\Yii::$app->user->identity->role === 'manager') {
            $query->andWhere(['agency_id' => \Yii::$app->user->identity->agency_id]);
        }

        // define filters for paginated pages
        if (null !== \Yii::$app->request->get('agency') && !empty(\Yii::$app->request->get('agency'))) {
            $agencyFilter = \Yii::$app->request->get('agency');
        }
        if (null !== \Yii::$app->request->get('agent') && !empty(\Yii::$app->request->get('agent'))) {
            $agentFilter = \Yii::$app->request->get('agent');
        }
        if (null !== \Yii::$app->request->get('category') && !empty(\Yii::$app->request->get('category'))) {
            $categoryFilter = \Yii::$app->request->get('category');
        }

        // apply filters (if defined)
        if (isset($agencyFilter) && $agencyFilter !== null) {
            $query->andWhere(['agency_id' => $agencyFilter]);
        }
        if (isset($agentFilter) && $agentFilter !== null) {
            $query->andWhere(['author_id' => $agentFilter]);
        }
        if (isset($categoryFilter) && $categoryFilter !== null) {
            $query->join('INNER JOIN', 'secondary_room', 'secondary_room.advertisement_id = secondary_advertisement.id')->andWhere(['secondary_room.category_id' => $categoryFilter]);
        }

        // get the total number of advertisements
        $count = $query->count();

        // create a pagination object with the total count
        $pagination = new Pagination(['totalCount' => $count]);

        if (!empty(\Yii::$app->request->get('psize'))) {
            $pagination->setPageSize(\Yii::$app->request->get('psize'));
        }

        // limit the query using the pagination and retrieve the advertisements
        $advertisements = $query
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->orderBy(['creation_date' => SORT_DESC])
            ->all();

        $advertisementsArray = [];
        foreach ($advertisements as $advertisement) {
            $advertisementItem = ArrayHelper::toArray($advertisement);
            $itemStatusLabels = [];
            foreach ($advertisement->statusLabels as $statusLabel) {
                $labelItem = ArrayHelper::toArray($statusLabel);
                $labelItem['type'] = ArrayHelper::toArray($statusLabel->labelType);
                array_push($itemStatusLabels, $labelItem);
            }
            $advertisementItem['statusLabels'] = $itemStatusLabels;
            $roomsArray = [];
            foreach ($advertisement->secondaryRooms as $room) {
                $roomItem = ArrayHelper::toArray($room);

                $roomItem['agent_fee'] = ArrayHelper::toArray($room->agentFee);
                
                // Add information about room params from data base
                $roomItem = $this->processRoomParameters($room, $roomItem);

                array_push($roomsArray, $roomItem);
            }
            $advertisementItem['rooms'] = $roomsArray;
            array_push($advertisementsArray, $advertisementItem);
        }

        return $this->inertia('User/Secondary/Index', [
            'user' => \Yii::$app->user->identity,
            'flash' => \Yii::$app->session->getAllFlashes(),
            'secondaryCategories' => SecondaryCategory::getCategoryTree(),
            'agencies' => \Yii::$app->user->identity->role === 'admin' ? Agency::getAllAsList() : [],
            'agents' => isset($agents) ? ArrayHelper::toArray($agents) : [],
            'advertisements' => $advertisementsArray,
            'totalRows' => $count,
            'page' => $pagination->page,
            'psize' => $pagination->pageSize,
            'filter' => [
                'agency' => isset($agencyFilter) ? $agencyFilter : null,
                'agent' => isset($agentFilter) ? $agentFilter : null,
                'category' => isset($categoryFilter) ? $categoryFilter : null,
            ],
            'labelTypes' => StatusLabelType::find()->asArray()->all(),
        ]);
    }


    /**
     * Processes room parameters and adds corresponding database шт to the room item.
     *
     * @param array $room The room data array
     * @param array $roomItem The room item array to be modified
     * @return array The modified room item with added parameter references
     */
    protected function processRoomParameters(object $room, array $roomItem): array
    {
        $params = [
            'category' => 'secondaryCategory', // category (e.g. 'flat', 'house' etc.)
            'property_type' => 'secondaryPropertyType',
            'building_series' => 'secondaryBuildingSeries',
            'newbuilding_complex' => 'newbuildingComplex',
            'newbuilding' => 'newbuilding',
            'entrance' => 'entrance',
            'flat' => 'flat',
            'renovation' => 'secondaryRenovation',
            'material' => 'buildingMaterial',
            'region' => 'region',
            'region_district' => 'regionDistrict',
            'city' => 'city',
            'district' => 'district',
            'street_type' => 'streetType',
        ];

        foreach ($params as $param => $className) {
            if (!empty($room->{$param.'_id'})) {
                $roomItem[$param.'_DB'] = ArrayHelper::toArray($room->{$className});
            }
        }
        
        return $roomItem;
    }

    /**
     * Finds the Commercial model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * 
     * @param integer $id
     * @return Commercial the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Commercial::findOne($id)) === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }

        return $model;
    }
}
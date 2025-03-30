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
                /*case 'filterAdds':
                    if (null !== \Yii::$app->request->post('agency') && !empty(\Yii::$app->request->post('agency'))) {
                        $agents = Agency::getUsersByAgency(\Yii::$app->request->post('agency'));
                    }
                    $agencyFilter = \Yii::$app->request->post('agency') !== null && !empty(\Yii::$app->request->post('agency')) ? \Yii::$app->request->post('agency') : null;
                    $agentFilter = \Yii::$app->request->post('agent') !== null && !empty(\Yii::$app->request->post('agent')) ? \Yii::$app->request->post('agent') : null;
                    $categoryFilter = \Yii::$app->request->post('category') !== null && !empty(\Yii::$app->request->post('category')) ? \Yii::$app->request->post('category') : null;
                    break;*/
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

        $advertisementsArray = array();
        foreach ($advertisements as $advertisement) {
            $advertisementItem = ArrayHelper::toArray($advertisement);
            $itemStatusLabels = array();
            foreach ($advertisement->statusLabels as $statusLabel) {
                $labelItem = ArrayHelper::toArray($statusLabel);
                $labelItem['type'] = ArrayHelper::toArray($statusLabel->labelType);
                array_push($itemStatusLabels, $labelItem);
            }
            $advertisementItem['statusLabels'] = $itemStatusLabels;
            $roomsArray = array();
            foreach ($advertisement->secondaryRooms as $room) {
                $roomItem = ArrayHelper::toArray($room);

                $roomItem['agent_fee'] = ArrayHelper::toArray($room->agentFee);

                /**
                 * Add information about room params from data base
                 */
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
                    if (!empty($room[$param.'_id'])) {
                        $roomItem[$param.'_DB'] = ArrayHelper::toArray($room[$className]);
                    }
                }
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

    
    public function actionView($id)
    {
        $commercialModel =  $this->findModel($id);

        $commercialArray = ArrayHelper::toArray($commercialModel);
        $commercialArray['initiator'] = ArrayHelper::toArray($commercialModel->initiator);
        $commercialArray['initiator']['organization'] = ArrayHelper::toArray($commercialModel->initiator->agency);

        $viewOptions = [
            'commercial' => $commercialArray,
        ];
        
        /** Commercial operations */
        if(\Yii::$app->request->isPost)  {
            switch(\Yii::$app->request->post('operation')) {
                /** Generate PDF-file */
                case 'pdf':
                    $this->downloadPdf($commercialModel->id);
                    $viewOptions = array_merge($viewOptions, [
                        'id' => $commercialModel->id,
                        'operation' => 'pdf',
                        'status' => 'ok'
                    ]);
                    break;
                case 'email':
                    \Yii::$app->mailer->compose()
                    ->setTo(\Yii::$app->request->post('email'))
                    ->setFrom([\Yii::$app->params['senderEmail'] => \Yii::$app->params['senderName']])
                    ->setSubject(\Yii::t('app', 'Коммерческое предложение №  '. $commercialModel->number))
                    ->setTextBody('Ссылка на страницу коммерческого предложения: https://grch.ru/share/commercial?id='.$commercialModel->id)
                    ->send();
                    break;
                /** Ghange commercial settings */
                case 'settings':
                    $commercialModel->settings = json_encode(\Yii::$app->request->post('settings'));
                    $commercialModel->save();
                    break;
                /** Deleting a flat from commercial */
                case 'removeFlat':
                    $flat = Flat::find()
                        ->where(['id' => \Yii::$app->request->post('flatId')])
                        ->one();
                    $commercialModel->unlink('flats', $flat, true);
                    break;
            }
        }
        
        $flats = $commercialModel->flats;
        $flatsArray = array();
        foreach ($flats as $flat) {

            /** creating floor layout with selected flat as svg file */
            $selectionLayout = (new Layout())->createFloorLayoutWithSelectedFlat($flat);

            $flatItem = ArrayHelper::toArray($flat);
            $flatItem['floorLayoutImage'] = !is_null($flat->floorLayout) ? $flat->floorLayout->image : NULL;
            $flatItem['developer'] = ArrayHelper::toArray($flat->developer);
            $flatItem['newbuildingComplex'] = ArrayHelper::toArray($flat->newbuilding->newbuildingComplex);
            $flatItem['newbuildingComplex']['address'] = $flat->newbuilding->newbuildingComplex->address;
            foreach ($flat->furnishes as $key => $furnish) {
                $finishing = ArrayHelper::toArray($furnish);
                $finishing['furnishImages'] = $furnish->furnishImages;
                $flatItem['finishing'][] = ArrayHelper::toArray($finishing);
            }
            $flatItem['newbuilding'] = ArrayHelper::toArray($flat->newbuilding);
            $flatItem['entrance'] = ArrayHelper::toArray($flat->entrance);
            $flatItem['advantages'] = ArrayHelper::toArray($flat->newbuilding->newbuildingComplex->advantages);
            array_push($flatsArray, $flatItem);
        }

        $commercialMode = count($flatsArray) > 1 ? 'multiple' : 'single';

        $viewOptions['flats'] = $flatsArray;
        $viewOptions['commercialMode'] = $commercialMode;

        return $this->inertia('User/Commercial/View', $viewOptions);
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
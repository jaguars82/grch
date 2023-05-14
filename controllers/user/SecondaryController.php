<?php

namespace app\controllers\user;

use app\components\traits\CustomRedirects;
use app\components\SharedDataFilter;
use app\models\SecondaryAdvertisement;
use app\models\SecondaryCategory;
use app\models\SecondaryRoom;
use app\models\SecondaryRenovation;
use app\models\BuildingMaterial;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use tebe\inertia\web\Controller;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\data\Pagination;

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
        return $this->inertia('User/Secondary/Create', [
            'user' => \Yii::$app->user->identity,
            'secondaryCategories' => SecondaryCategory::getCategoryTree(),
            'buildingMaterials' => BuildingMaterial::getMaterialList(),
            'renovations' => SecondaryRenovation::getRenovationList(),
            'bathroomUnit' => SecondaryRoom::$bathroom,
            'quality' => SecondaryRoom::$quality,
        ]);
    }

    public function actionIndex()
    {
        $query = SecondaryAdvertisement::find()
        //->where(['initiator_id' => \Yii::$app->user->id])
        ->where(['is_active' => 1]);

        // get the total number of advertisements
        $count = $query->count();

        // create a pagination object with the total count
        $pagination = new Pagination(['totalCount' => $count]);

        // limit the query using the pagination and retrieve the advertisements
        $advertisements = $query
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->orderBy(['creation_date' => SORT_DESC])
            ->all();

        $advertisementsArray = array();
        foreach ($advertisements as $advertisement) {
            $advertisementItem = ArrayHelper::toArray($advertisement);
            $roomsArray = array();
            foreach ($advertisement->secondaryRooms as $room) {
                $roomItem = ArrayHelper::toArray($room);
                //$flatItem['newbuildingComplex'] = ArrayHelper::toArray($flat->newbuilding->newbuildingComplex);
                //$flatItem['newbuilding'] = ArrayHelper::toArray($flat->newbuilding);
                array_push($roomsArray, $roomItem);
            }
            $advertisementItem['rooms'] = $roomsArray;
            array_push($advertisementsArray, $advertisementItem);
        }

        return $this->inertia('User/Secondary/Index', [
            'user' => \Yii::$app->user->identity,
            'advertisements' => $advertisementsArray,
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
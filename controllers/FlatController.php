<?php

namespace app\controllers;

use app\components\traits\CustomRedirects;
use app\components\SharedDataFilter;
use app\models\Newbuilding;
use app\models\NewbuildingComplex;
use app\models\form\FlatForm;
use app\models\service\Flat;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use tebe\inertia\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\FloorLayout;
use yii\helpers\Url;

/**
 * FlatController implements the CRUD actions for Flat model.
 */
class FlatController extends Controller
{
    use CustomRedirects;
    
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['view'],
                        'roles' => ['@'],
                    ],
                ]
            ],
            [
                'class' => SharedDataFilter::class
            ],
        ];
    }

    /**
     * Displays a single Flat model.
     * 
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id, true);
        
        $flatDataProvider = new ActiveDataProvider([
            'query' => $model->newbuildingComplex->getFlats()->with(['flatImages', 'developer', 'favorites'])->analogs($model)->limit(3),
            'pagination' => false,
            'sort' => ['attributes' => ['id'], 'defaultOrder' => ['id' => SORT_DESC]],
        ]);
        
        $contactDataProvider = new ActiveDataProvider([
            'query' => $model->newbuildingComplex->getContacts(),
            'pagination' => false,
            'sort' => ['attributes' => ['id'], 'defaultOrder' => ['id' => SORT_DESC]],
        ]);

        $newbuildingComplexDataProvider = new ActiveDataProvider([
            //'query' => NewbuildingComplex::find()->forDeveloper($model->developer->id)->onlyActive()->onlyWithActiveBuildings()->where(['!=', 'id', $model->newbuildingComplex->id])->limit(6),
            'query' => NewbuildingComplex::find()->onlyActive()->onlyWithActiveBuildings()->andWhere(['!=', 'id', $model->newbuildingComplex->id])->andWhere(['=', 'developer_id', $model->developer->id])/*->limit(6)*/,
            'pagination' => false,
            'sort' => ['attributes' => ['id'], 'defaultOrder' => ['id' => SORT_DESC]],
        ]);

        /*return $this->render('view', [
            'model' => $model,
            'floorLayoutImage' => !is_null($model->floorLayoutSvg) ? $model->floorLayoutSvg : NULL,
            'flatDataProvider' => $flatDataProvider,
            'contactDataProvider' => $contactDataProvider,
            'newbuildingComplexDataProvider' => $newbuildingComplexDataProvider,
        ]);*/

        return $this->inertia('Flat/View', [
            'flat' => ArrayHelper::toArray($model, [
                'app\models\service\Flat' => [
                    'id', 'newbuilding_id', 'entrance_id', 'address', 'detail', 'area', 'rooms', 'floor', 'index_on_floor', 'price_cash', 'status', 'sold_by_application', 'is_applicated', 'is_reserved', 'created_at', 'updated_at', 'unit_price_cash', 'discount_type', 'discount', 'discount_amount', 'discount_price', 'azimuth', 'notification', 'extra_data', 'composite_flat_id', 'section', 'number', 'layout', 'unit_price_credit', 'price_credit', 'floor_position', 'floor_layout', 'layout_coords', 'is_euro', 'is_studio',
                    'entranceAzimuth' => function ($flat) { return $flat->entrance->azimuth; },
                    'masterPlan' => function ($flat) { return $flat->newbuildingComplex->master_plan; },
                    'floorBackground' => function ($flat) { return $flat->floorLayout !== null ? $flat->floorLayout->image : null; },
                    'svgViewBox' => function ($flat) { return $flat->floorLayoutSvgViewBox; },
                    'neighboringFlats' => function ($flat) { return ArrayHelper::toArray($flat->getNeighboringFlats()->all()); },
                    //'floorLayoutImage' => function ($flat) { return $flat->floorLayoutSvg; },
                    'complex' => function ($flat) {
                        return ArrayHelper::toArray($flat->newbuildingComplex, [
                            'app\models\NewbuildingComplex' => [
                                'id', 'developer_id', 'name', 'longitude', 'latitude', 'logo', 'detail', 'offer_new_price_permit', 'project_declaration', 'algorithm', 'offer_info', 'created_at', 'updated_at', 'active', 'region_id', 'city_id', 'district_id', 'street_type_id', 'street_name', 'building_type_id', 'building_number', 'master_plan' 
                            ]
                        ]);
                    }
                ]
            ]),
            'otherNC' => ArrayHelper::toArray($newbuildingComplexDataProvider->getModels()),
        ]);
    }

    /**
     * Finds the Flat model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * 
     * @param integer $id
     * @return Flat the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id, $withRelatedModels = false)
    {
        $query = Flat::find()->where(['id' => $id])->with(['newbuildingComplex']);
        
        if ($withRelatedModels) {
            $query->with(['newbuildingComplex.furnishes.furnishImages']);
        }
        
        if (($model = $query->one()) === null || $model->newbuildingComplex->active == false) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }

        return $model;
    }
}

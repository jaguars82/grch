<?php

namespace app\controllers;

use Yii;
use app\models\City;
use app\models\District;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\components\traits\CustomRedirects;
use app\components\SharedDataFilter;

/**
 * CityController implements the CRUD actions for City model.
 */
class CityController extends Controller
{
    use CustomRedirects;

    public $layout = 'admin';

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
                        'actions' => ['get-for-region', 'get-for-city'],
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
     * Getting cities for given region.
     * 
     * @param integer $id Region ID
     * @return mixed
     */
    public function actionGetForRegion($id, $withNewbuildingComplexes = true)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;        
        
        $query = City::find();

        if ($withNewbuildingComplexes) {
            $query->innerJoin('newbuilding_complex', 'newbuilding_complex.city_id = city.id');
        }

        $cities = $query
            ->where(['city.region_id' => $id])
            ->select(['city.id', 'city.name', 'is_region_center'])
            ->orderBy(['city.name' => SORT_DESC])
            ->asArray()
            ->all();

        $regionCenter = [];
        $sattlements = [];

        foreach ($cities as $city) {
            if ($city['is_region_center'] == 1) {
                array_push($regionCenter, $city);
            } else {
                array_push($sattlements, $city);
            }
        }

        $result = count($regionCenter) > 0 ? array_merge($regionCenter, $sattlements) : $sattlements;

        \Yii::$app->response->data = $result;
    }

    /**
     * Getting district for given city.
     * 
     * @param integer $id
     * @return mixed
     */
    public function actionGetForCity($id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;        
        \Yii::$app->response->data = District::find()
            ->forCity($id)
            ->select(['id', 'name'])
            ->orderBy(['id' => SORT_DESC])
            ->asArray()
            ->all();
    }

    /**
     * Finds the City model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return City the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = City::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

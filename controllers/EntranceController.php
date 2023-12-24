<?php

namespace app\controllers;

use app\components\exceptions\AppException;
use app\components\traits\CustomRedirects;
use app\models\Entrance;
use app\models\Flat;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\components\SharedDataFilter;

class EntranceController extends Controller
{
    use CustomRedirects;

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['get-for-newbuilding', 'get-flats-by-entrance', 'get-risers-by-entrances'],
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
     * Getting entrances for given newbuilding.
     * 
     * @param integer $id newbuilding's ID
     * @return mixed
     */
    public function actionGetForNewbuilding($id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;        
        $entrances = Entrance::find()
            ->forNewbuilding($id)
            //->select(['id', 'name'])
            //->asArray()
            ->all();
        
        foreach ($entrances as &$entrance) {
            //$entrance['name'] = \Yii::$app->formatter->asCapitalize($entrance['name']);
            $entrance->name = \Yii::$app->formatter->asCapitalize($entrance->name);
            $entrance->name = $entrance->name.', ('.$entrance->newbuilding->name.')';
        }
        
        \Yii::$app->response->data = $entrances;
    }

    
    /**
     * Getting flats for given entrance.
     * 
     * @param integer $id mewbuilding's ID
     * @return mixed
     */
    public function actionGetFlatsByEntrance($id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;        
        $flats = Flat::find()
            ->select(['number'])
            ->where(['entrance_id' => $id])
            ->andWhere(['status' => 0])
            ->orderBy(['number' => SORT_ASC])
            ->all();
        
        \Yii::$app->response->data = $flats;
    }

    
    /**
     * Get array with index_on_floor range (for particular entrances or for entyre table)
     */
    public static function actionGetRisersByEntrances($entranceId)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $maxIndex = Flat::find()
            ->where(['entrance_id' => $entranceId])
            ->max('index_on_floor');

        //$maxIndex = 5;

        $rises = array();

        for ($i = 1; $i <= $maxIndex; $i++) {
            array_push($rises, $i);
        }

        \Yii::$app->response->data = $rises;
    }

}
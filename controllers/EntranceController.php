<?php

namespace app\controllers;

use app\components\exceptions\AppException;
use app\components\traits\CustomRedirects;
use app\models\Entrance;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * NewbuildingController implements the CRUD actions for Newbuilding model.
 */
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
                        'actions' => ['get-for-newbuilding'],
                        'roles' => ['@'],
                    ],
                ]
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
}
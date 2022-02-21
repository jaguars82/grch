<?php

namespace app\controllers;

use app\components\traits\CustomRedirects;
use app\models\FloorLayout;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;

/**
 * FloorLayoutController implements the CRUD actions for FloorLayout model.
 */
class FloorLayoutController extends Controller
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
                        'actions' => ['check'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ]
            ],
        ];
    }

    /**
     * Check floor layout floor and section
     * 
     * @return mixed
     */
    public function actionCheck()
    {
        $floor_layout_id = \Yii::$app->request->post('floor_layout_id', NULL);
        $newbuilding_id = \Yii::$app->request->post('newbuilding_id');
        $modelFloor = \Yii::$app->request->post('model_floor');
        $modelSection = \Yii::$app->request->post('model_section');
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        if (($result = \app\components\validators\FloorAndSectionValidator::execute($floor_layout_id, $newbuilding_id, $modelFloor, $modelSection))) {
            \Yii::$app->response->statusCode = 401;
            \Yii::$app->response->data = [
                'message' => $result
            ];
            return;
        }
    }

    /**
     * Finds the FloorLayout model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * 
     * @param integer $id
     * 
     * @return FloorLayout the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = FloorLayout::findOne($id)) !== null) {
            return $model; 
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

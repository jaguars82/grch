<?php

namespace app\controllers\admin;

use Yii;
use app\models\City;
use app\models\District;
use app\models\search\DistrictSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\components\traits\CustomRedirects;

/**
 * DistrictController implements the CRUD actions for District model.
 */
class DistrictController extends Controller
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
                        'actions' => ['index', 'create', 'update', 'delete'],
                        'roles' => ['admin'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['get-for-city'],
                        'roles' => ['@'],
                    ],
                ]
            ]
        ];
    }

    /**
     * Lists all NewbuildingComplex models.
     * 
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DistrictSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'itemsCount' => $searchModel->itemsCount,
            'cities' => City::getAllAsList(),
        ]);
    }

    /**
     * Creates a new District model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new District();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirectWithSuccess(['index'], 'Район успешно добавлен');
        }

        return $this->render('create', [
            'model' => $model,
            'cities' => City::getAllAsList(),
        ]);
    }

    /**
     * Updates an existing District model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirectWithSuccess(['index'], 'Район успешно обновлен');
        }

        return $this->render('update', [
            'model' => $model,
            'cities' => City::getAllAsList(),
        ]);
    }

    /**
     * Deletes an existing District model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirectWithSuccess(['index'], 'Район удален');
    }

    /**
     * Finds the District model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return District the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = District::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    
    /**
     * Getting district for given city.
     * 
     * @param integer $id City ID
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
}

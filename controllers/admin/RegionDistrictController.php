<?php

namespace app\controllers\admin;

use Yii;
use app\models\City;
use app\models\Region;
use app\models\RegionDistrict;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\components\traits\CustomRedirects;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\models\search\RegionDistrictSearch;

/**
 * RegionDistrictController implements the CRUD actions for City model.
 */
class RegionDistrictController extends Controller
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
                        'actions' => ['get-for-region'],
                        'roles' => ['@'],
                    ],
                ]
            ],
        ];
    }

    /**
     * Lists all RegionDistrict models.
     * @return mixed
     */
    public function actionIndex()
    {

        $searchModel = new RegionDistrictSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'itemsCount' => $searchModel->itemsCount,
            'regions' => Region::getAllAsList(),
        ]);
    }

    /**
     * Creates a new RegionDistrict model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new RegionDistrict();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirectWithSuccess(['index'], 'Административный район успешно добавлен');
        }

        return $this->render('create', [
            'model' => $model,
            'regions' => Region::getAllAsList(),
        ]);
    }

    /**
     * Updates an existing RegionDistrict model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirectWithSuccess(['index'], 'Административный район успешно обновлен');
        }

        return $this->render('update', [
            'model' => $model,
            'regions' => Region::getAllAsList(),
        ]);
    }

    /**
     * Deletes an existing RegionDistrict model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirectWithSuccess(['index'], 'Административный район удален');
    }

    /**
     * Finds the RegionDistrict model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return RegionDistrict the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = RegionDistrict::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Административного района с таким ID не существует');
    }

    /**
     * Getting districts for given region.
     * 
     * @param integer $id Region ID
     * @return mixed
     */
    public function actionGetForRegion($id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;        
        \Yii::$app->response->data = RegionDistrict::find()
            //->forRegion($id)
            ->where(['region_id' => $id])
            ->select(['id', 'name'])
            ->orderBy(['name' => SORT_ASC])
            ->asArray()
            ->all();
    }
}

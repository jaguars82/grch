<?php

namespace app\controllers;

use app\components\exceptions\AppException;
use app\components\traits\CustomRedirects;
use app\models\Bank;
use app\models\Developer;
use app\models\Newbuilding;
use app\models\Document;
use app\models\form\NewbuildingComplexForm;
use app\models\form\ProjectDeclarationForm;
use app\models\search\NewbuildingComplexSearch;
use app\models\search\NewbuildingComplexFlatSearch;
use app\models\service\NewbuildingComplex;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * NewbuildingComplexController implements the CRUD actions for NewbuildingComplex model.
 */
class NewbuildingComplexController extends Controller
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
                    'get-for-developer' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'get-for-developer', 'download-document'],
                        'roles' => ['@'],
                    ],
                ]
            ],
        ];
    }
    
    /**
     * Lists all NewbuildingComplex models.
     * 
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new NewbuildingComplexSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'itemsCount' => $searchModel->itemsCount,
            'developers' => Developer::getAllAsList(),
        ]);
    }

    /**
     * Displays a single NewbuildingComplex model.
     * 
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id, true);       
        $positions = [];
        $positionArray = [];
        $number = 1;
        foreach ($model->getNewbuildings()->with(['activeFlats'])->all() as $newbuilding) {
            $positions[] = [
                'name' => $newbuilding->name,
                'number' => $number++,
                'developer_id' => $newbuilding->newbuildingComplex->developer_id,
                'newbuilding_complex_id' => $newbuilding->newbuilding_complex_id,
                'newbuilding_id' => $newbuilding->id,
                'flats' => count($newbuilding->activeFlats),
                'status' => $newbuilding->status,
                'floors' => $newbuilding->total_floor,
            ];
            $positionArray[$newbuilding->id] = \Yii::$app->formatter->asCapitalize($newbuilding->name);
        }
        
        $newsDataProvider = new ActiveDataProvider([
            'query' => $model->getNews()->limit(4),
            'pagination' => false,
            'sort' => ['attributes' => ['created_at'], 'defaultOrder' => ['created_at' => SORT_DESC]],
        ]);
        
        $contactDataProvider = new ActiveDataProvider([
            'query' => $model->getContacts(),
            'pagination' => false,
            'sort' => ['attributes' => ['id'], 'defaultOrder' => ['id' => SORT_DESC]],
        ]);
        
        $newbuildingComplexesDataProvider = new ActiveDataProvider([
            'query' => NewbuildingComplex::find()->onlyActive()->andWhere(['!=', 'id', $model->id])->andWhere(['=', 'developer_id', $model->developer_id])->limit(6),
            'pagination' => false,
            'sort' => ['attributes' => ['id'], 'defaultOrder' => ['id' => SORT_DESC]],
        ]);

        return $this->render('view', [
            'model' => $model,
            'projectDeclaration' => new ProjectDeclarationForm(),
            'newsDataProvider' => $newsDataProvider,
            'newbuildingComplexesDataProvider' => $newbuildingComplexesDataProvider,
            'contactDataProvider' => $contactDataProvider,
        ]);
    }

    /**
     * Finds the NewbuildingComplex model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * 
     * @param integer $id
     * @return NewbuildingComplex the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id, $withRelatedModels = false)
    {
        $query = NewbuildingComplex::find()->where(['id' => $id]);
        
        if ($withRelatedModels) {
            $query->with(['furnishes.furnishImages']);
        }
        
        if (($model = $query->one()) === null || $model->active == false) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }

        return $model;
    }

    /**
     * Getting newbuilding complexes for given developer.
     * 
     * @param integer $id Developer's ID
     * @return mixed
     */
    public function actionGetForDeveloper($id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;        
        \Yii::$app->response->data = NewbuildingComplex::find()
            ->forDeveloper($id)
            ->onlyActive(true)
            ->select(['id', 'name'])
            ->orderBy(['id' => SORT_DESC])
            ->asArray()
            ->all();
    }

    /**
     * Getting file for given Newbuilding Complex.
     * 
     * @param integer $id Newbuilding Complexes ID
     * @param integer $file Newbuilding Complexes file origin name
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDownloadDocument($id)
    {
        if (($document = Document::findOne($id)) == null) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }

        $fileName = \Yii::getAlias("@webroot/uploads/{$document->file}");
        return \Yii::$app->response->sendFile($fileName, "{$document->name}." . pathinfo($fileName, PATHINFO_EXTENSION));
    }
}

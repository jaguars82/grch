<?php

namespace app\controllers;

use app\components\exceptions\AppException;
use app\components\traits\CustomRedirects;
use app\components\SharedDataFilter;
use app\components\VisitBehavior;
use app\models\Newbuilding;
use app\models\Flat;
use app\models\NewbuildingComplex;
use app\models\search\NewbuildingFlatSearch;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * NewbuildingController implements the CRUD actions for Newbuilding model.
 */
class NewbuildingController extends Controller
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
                        'actions' => ['view'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['get-for-newbuilding-complex', 'get-flats-by-newbuilding'],
                        'roles' => ['@'],
                    ],
                ]
            ],
            [
                'class' => SharedDataFilter::class
            ],
            'visit' => VisitBehavior::class,
        ];
    }

    /**
     * Displays a single Newbuilding model.
     * 
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        $contactDataProvider = new ActiveDataProvider([
            'query' => $model->newbuildingComplex->getContacts(),
            'pagination' => false,
            'sort' => ['attributes' => ['id'], 'defaultOrder' => ['id' => SORT_DESC]],
        ]);

        $floorLayoutsData = $model->getFloorLayouts()->orderBy('cast(section as unsigned) asc, cast(floor as unsigned) asc')->all();
        $floorLayouts = [];

        foreach($floorLayoutsData as $floorLayout) {
            $floorLayouts[$floorLayout->section][] = $floorLayout;
        }

        $newsDataProvider = new ActiveDataProvider([
            'query' => $model->getNews()->limit(4),
            'pagination' => false,
            'sort' => ['attributes' => ['created_at'], 'defaultOrder' => ['created_at' => SORT_DESC]],
        ]);
        
        $newbuildingComplexesDataProvider = new ActiveDataProvider([
            //'query' => NewbuildingComplex::find()->forDeveloper($model->developer->id)->onlyActive()->onlyWithActiveBuildings()->where(['!=', 'id', $model->newbuildingComplex->id])->limit(6),
            'query' => NewbuildingComplex::find()->onlyActive()->onlyWithActiveBuildings()->andWhere(['!=', 'id', $model->newbuildingComplex->id])->andWhere(['=', 'developer_id', $model->developer->id])/*->limit(6)*/,
            'pagination' => false,
            'sort' => ['attributes' => ['id'], 'defaultOrder' => ['id' => SORT_DESC]],
        ]); 
        
        return $this->render('view', [
            'model' => $model,
            'contactDataProvider' => $contactDataProvider,
            'newsDataProvider' => $newsDataProvider,
            'newbuildingComplexesDataProvider' => $newbuildingComplexesDataProvider,
            'floorLayouts' => $floorLayouts,
        ]);
    }

    /**
     * Getting newbuildings for given newbuilding complex.
     * 
     * @param integer $id mewbuilding complex'es ID
     * @return mixed
     */
    public function actionGetForNewbuildingComplex($id)
    {
        $idies = explode(',', $id);
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;        
        $newbuildings = Newbuilding::find()
            ->forNewbuildingComplex($idies)
            ->select(['id', 'name'])
            ->asArray()
            ->all();
        
        foreach ($newbuildings as &$newbuilding) {
            $newbuilding['name'] = \Yii::$app->formatter->asCapitalize($newbuilding['name']);
        }
        
        \Yii::$app->response->data = $newbuildings;
    }

    /**
     * Getting flats for given newbuilding.
     * 
     * @param integer $id mewbuilding's ID
     * @return mixed
     */
    public function actionGetFlatsByNewbuilding($id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;        
        $flats = Flat::find()
            ->select(['number'])
            ->where(['newbuilding_id' => $id])
            ->andWhere(['status' => 0])
            ->orderBy(['number' => SORT_ASC])
            ->all();
        
        \Yii::$app->response->data = $flats;
    }

    /**
     * Finds the Newbuilding model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * 
     * @param integer $id
     * @return Newbuilding the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Newbuilding::findOne($id)) === null || $model->newbuildingComplex->active == false) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }

        return $model;
    }
}

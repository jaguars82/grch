<?php

namespace app\controllers\admin;

use app\components\exceptions\AppException;
use app\components\traits\CustomRedirects;
use app\models\Region;
use app\models\City;
use app\models\StreetType;
use app\models\BuildingType;
use app\models\Advantage;
use app\models\service\Newbuilding;
use app\models\NewbuildingComplex;
use app\models\form\NewbuildingForm;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;

/**
 * NewbuildingController implements the CRUD actions for Newbuilding model.
 */
class NewbuildingController extends Controller
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
                        'actions' => ['create', 'update', 'delete', 'index'],
                        'roles' => ['admin'],
                    ],
                ]
            ],
        ];
    }

    public function actionIndex($newbuildingComplexId)
    {
        if (($newbuildingComplex = NewbuildingComplex::findOne($newbuildingComplexId)) === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }

        $dataProvider = new ActiveDataProvider([
            'query' => Newbuilding::find()
                ->where(['newbuilding_complex_id' => $newbuildingComplexId]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'newbuildingComplex' => $newbuildingComplex,
        ]);
    }

    /**
     * Creates a new Newbuilding model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * 
     * @return mixed
     */
    public function actionCreate($newbuildingComplexId)
    {
        if (($newbuildingComplex = NewbuildingComplex::findOne($newbuildingComplexId)) === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }
        
        $form = new NewbuildingForm();
        $form->newbuilding_complex_id = $newbuildingComplex->id;

        if (\Yii::$app->request->isPost && $form->load(\Yii::$app->request->post()) && $form->process()) {         
            try {
                Newbuilding::create($form->attributes);
            } catch (\Exception $e) {
                return $this->redirectBackWhenException($e);
            }
            
            return $this->redirectWithSuccess(['index', 'newbuildingComplexId' => $newbuildingComplex->id], 'Добавлена позиция');
        }

        return $this->render('create', [
            'model' => $form,
            'regions' => Region::getAllAsList(),
            'cities' => !is_null($form->region_id) ? City::find()->forRegion($form->region_id)->all() : [],
            'streetTypes' => StreetType::getAllAsList(),
            'buildingTypes' => BuildingType::getAllAsList(),
            'advantages' => Advantage::getAllAsList(),
            'newbuildingComplex' => $newbuildingComplex
        ]);
    }

    /**
     * Updates an existing Newbuilding model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * 
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $form = (new NewbuildingForm())->fill($model->attributes);
        $form->scenario = NewbuildingForm::SCENARIO_UPDATE;
        $form->advantages = ArrayHelper::getColumn($model->getAdvantages()->asArray()->all(), 'id');

        if (\Yii::$app->request->isPost && $form->load(\Yii::$app->request->post()) && $form->process()) {
            try {
                $model->edit($form->attributes);
            } catch (\Exception $e) {
                return $this->redirectBackWhenException($e);
            }
            
            return $this->redirectWithSuccess(['index', 'newbuildingComplexId' => $model->newbuildingComplex->id], 'Позиция обновлена');
        }

        return $this->render('update', [
            'model' => $form,
            'newbuilding' => $model,
            'regions' => Region::getAllAsList(),
            'cities' => !is_null($model->region_id) ? City::find()->forRegion($model->region_id)->all() : [],
            'streetTypes' => StreetType::getAllAsList(),
            'buildingTypes' => BuildingType::getAllAsList(),
            'advantages' => Advantage::getAllAsList(),
            'newbuildingComplex' => $model->newbuildingComplex
        ]);
    }

    /**
     * Deletes an existing Newbuilding model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * 
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $newbuildingComplexId = $model->newbuilding_complex_id;
        
        try {
            if (count($model->flats)) {
                throw new AppException('Позиция не может быть удалена, так как имеет квартиры');
            }            
            $model->delete();
        } catch (\Exception $e) {
            return $this->redirectBackWhenException($e);
        }

        return $this->redirectWithSuccess(['index', 'newbuildingComplexId' => $newbuildingComplexId], 'Позиция удалена');
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
        if (($model = Newbuilding::findOne($id)) === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }

        return $model;
    }
}

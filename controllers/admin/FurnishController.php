<?php

namespace app\controllers\admin;

use app\components\traits\CustomRedirects;
use app\models\service\Furnish;
use app\models\NewbuildingComplex;
use app\models\form\FurnishForm;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

/**
 * FurnishController implements the CRUD actions for Furnish model.
 */
class FurnishController extends Controller
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
            'query' => Furnish::find()->where(['newbuilding_complex_id' => $newbuildingComplex->id]),
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
     * Creates a new Furnish model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * 
     * @return mixed
     */
    public function actionCreate($newbuildingComplexId)
    {
        if (($newbuildingComplex = NewbuildingComplex::findOne($newbuildingComplexId)) === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }
        
        $form = new FurnishForm();
        $form->newbuilding_complex_id = $newbuildingComplex->id;

        if (\Yii::$app->request->isPost && $form->load(\Yii::$app->request->post()) && $form->process()) {         
            try {
                $model = Furnish::create($form->attributes);               
            } catch (\Exception $e) {
                return $this->redirectBackWhenException($e);
            }

            return $this->redirectWithSuccess(['admin/furnish/index', 'newbuildingComplexId' => $model->newbuilding_complex_id], 'Добавлена отделка');
        }

        return $this->render('create', [
            'model' => $form,
            'newbuildingComplex' => $newbuildingComplex,
        ]);
    }

    /**
     * Updates an existing Furnish model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * 
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);        
        $form = (new FurnishForm())->fill($model->attributes);
        $form->savedImages = $model->getFurnishImages()->indexBy('id')->asArray()->all();
        $form->scenario = FurnishForm::SCENARIO_UPDATE;

        if (\Yii::$app->request->isPost && $form->load(\Yii::$app->request->post()) && $form->process()) {
            try {
                $model->edit($form->attributes);               
            } catch (\Exception $e) {
                return $this->redirectBackWhenException($e);
            }

            return $this->redirectWithSuccess(['admin/furnish/index', 'newbuildingComplexId' => $model->newbuilding_complex_id], 'Отделка обновлена');
        }

        return $this->render('update', [
            'model' => $form,
            'furnish' => $model,
        ]);
    }

    /**
     * Deletes an existing Furnish model.
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
            $model->delete();
        } catch (\Exception $e) {
            return $this->redirectBackWhenException($e);
        }
        
        return $this->redirectWithSuccess(['admin/furnish/index', 'newbuildingComplexId' => $newbuildingComplexId], 'Отделка удалена');
    }

    /**
     * Finds the Furnish model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * 
     * @param integer $id
     * @return Furnish the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Furnish::findOne($id)) === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }

        return $model;
    }
}

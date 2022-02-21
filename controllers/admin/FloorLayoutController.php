<?php

namespace app\controllers\admin;

use app\components\traits\CustomRedirects;
use app\models\FloorLayout;
use app\models\Newbuilding;
use app\models\form\FloorLayoutForm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;

/**
 * FloorLayoutController implements the CRUD actions for FloorLayout model.
 */
class FloorLayoutController extends Controller
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

    public function actionIndex($newbuildingId)
    {
        if (($newbuilding = Newbuilding::findOne($newbuildingId)) === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }
        
        $dataProvider = new ActiveDataProvider([
            'query' => FloorLayout::find()->where(['newbuilding_id' => $newbuilding->id]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            'newbuilding' => $newbuilding,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Creates a new FloorLayout model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * 
     * @param type $newbuildingId
     * 
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    public function actionCreate($newbuildingId)
    {        
        if (($newbuilding = Newbuilding::findOne($newbuildingId)) === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }
        
        $form = new FloorLayoutForm();
        $form->newbuilding_id = $newbuilding->id;

        if (\Yii::$app->request->isPost && $form->load(\Yii::$app->request->post()) && $form->process()) {
            try {
                $model = (new FloorLayout())->fill($form->attributes);
                $model->save();
            } catch (\Exception $e) {
                return $this->redirectBackWhenException($e);
            }

            return $this->redirectWithSuccess(['admin/floor-layout/index', 'newbuildingId' => $model->newbuilding_id], 'Добавлена планировка этажа');
        }

        return $this->render('create', [
            'model' => $form,
            'newbuilding' => $newbuilding,
        ]);
    }

    /**
     * Updates an existing FloorLayout model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * 
     * @param integer $id
     * 
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {        
        $model = $this->findModel($id);        
        $form = (new FloorLayoutForm())->fill($model->attributes);
        $form->floorLayoutId = $model->id;
        $form->scenario = FloorLayoutForm::SCENARIO_UPDATE;

        if (\Yii::$app->request->isPost && $form->load(\Yii::$app->request->post()) && $form->process()) {
            try {
                $model->fill($form->attributes, ['image']);
                $model->image = (!is_null($form->attributes['image'])) ? $form->attributes['image'] : $model->image; 
                $model->save();
            } catch (\Exception $e) {
                return $this->redirectBackWhenException($e);
            }

            return $this->redirectWithSuccess(['admin/floor-layout/index', 'newbuildingId' => $model->newbuilding_id], 'Планировка этажа обновлена');
        }
        
        return $this->render('update', [
            'model' => $form,
            'newbuilding' => $model->newbuilding,
            'floorLayout' => $model,
        ]);
    }

    /**
     * Deletes an existing FloorLayout model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * 
     * @param integer $id
     * 
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $newbuildingId = $model->newbuilding->id;
        
        try {
            $model->delete();
        } catch (\Exception $e) {
            return $this->redirectBackWhenException($e);
        }

        return $this->redirectWithSuccess(['admin/floor-layout/index', 'newbuildingId' => $newbuildingId], 'Планировка этажа удалена');
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

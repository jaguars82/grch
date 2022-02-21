<?php

namespace app\controllers\admin;

use app\components\traits\CustomRedirects;
use app\models\Newbuilding;
use app\models\form\FlatForm;
use app\models\service\Flat;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\components\flat\SvgDom;
use app\models\FloorLayout;

/**
 * FlatController implements the CRUD actions for Flat model.
 */
class FlatController extends Controller
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
            'query' => Flat::find()->where(['newbuilding_id' => $newbuilding->id]),
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
     * Creates a new Flat model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * 
     * @return mixed
     */
    public function actionCreate($newbuildingId)
    {
        if (($newbuilding = Newbuilding::findOne($newbuildingId)) === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }
        
        $form = new FlatForm();
        $form->newbuilding_id = $newbuilding->id;

        if (\Yii::$app->request->isPost && $form->load(\Yii::$app->request->post()) && $form->process()) {
            try {
                Flat::create($form->attributes);
            } catch (\Exception $e) {
                return $this->redirectBackWhenException($e);
            }
            
            return $this->redirectWithSuccess(['admin/flat/index', 'newbuildingId' => $newbuildingId], 'Добавлена квартира');
        }

        return $this->render('create', [
            'model' => $form,
            'newbuilding' => $newbuilding,
            'actions' => $newbuilding->newbuildingComplex->activeActions,
        ]);
    }

    /**
     * Updates an existing Flat model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * 
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);        
        $form = (new FlatForm())->fill($model->attributes);
        $form->floor_position = $model->floor_position;
        $form->savedActions = array_unique(ArrayHelper::getColumn($model->actions, 'id'));
        $form->savedImages = $model->getFlatImages()->indexBy('id')->asArray()->all();
        $form->scenario = FlatForm::SCENARIO_UPDATE;

        if($model->is_euro) {
            $form->layout_type = 'euro';
        } else if($model->is_studio) {
            $form->layout_type = 'studio';
        }

        if (\Yii::$app->request->isPost && $form->load(\Yii::$app->request->post()) && $form->process()) {
            try {
                $model->edit($form->attributes);
            } catch (\Exception $e) {
                return $this->redirectBackWhenException($e);
            }

            return $this->redirectWithSuccess(['admin/flat/index', 'newbuildingId' => $model->newbuilding_id], 'Квартира обновлена');
        }
        
        return $this->render('update', [
            'model' => $form,
            'newbuilding' => $model->newbuilding,
            'flat' => $model,
            'actions' => $model->newbuildingComplex->activeActions,
        ]);
    }

    /**
     * Deletes an existing Flat model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * 
     * @param integer $id
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

        return $this->redirectWithSuccess(['admin/flat/index', 'newbuildingId' => $newbuildingId], 'Квартира удалена');
    }

    /**
     * Finds the Flat model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * 
     * @param integer $id
     * @return Flat the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id, $withRelatedModels = false)
    {
        $query = Flat::find()->where(['id' => $id]);
        
        if ($withRelatedModels) {
            $query->with(['newbuildingComplex.furnishes.furnishImages']);
        }
        
        if (($model = $query->one()) === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }

        return $model;
    }
}

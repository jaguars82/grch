<?php

namespace app\controllers\admin;

use app\components\traits\CustomRedirects;
use app\models\Newbuilding;
use app\models\Entrance;
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

    public function actionIndex($entranceId)
    {
        /*if (($newbuilding = Newbuilding::findOne($newbuildingId)) === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }*/

        if (($entrance = Entrance::findOne($entranceId)) === null) {
            throw new NotFoundHttpException('Подъезд отсутсвует');
        }

        $dataProvider = new ActiveDataProvider([
            'query' => Flat::find()->where(['entrance_id' => $entrance->id]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            //'newbuilding' => $newbuilding,
            'entrance' => $entrance,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Creates a new Flat model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * 
     * @return mixed
     */
    public function actionCreate($entranceId)
    {
        if (($entrance = Entrance::findOne($entranceId)) === null) {
            throw new NotFoundHttpException('Подъезд не найден');
        }
        
        $form = new FlatForm();
        $form->entrance_id = $entrance->id;
        $form->newbuilding_id = $entrance->newbuilding->id;
        $form->section = $entrance->number;

        if (\Yii::$app->request->isPost && $form->load(\Yii::$app->request->post()) && $form->process()) {
            try {
                Flat::create($form->attributes);
            } catch (\Exception $e) {
                return $this->redirectBackWhenException($e);
            }
            
            return $this->redirectWithSuccess(['admin/flat/index', 'entranceId' => $entranceId], 'Добавлена квартира');
        }

        return $this->render('create', [
            'model' => $form,
            'entrance' => $entrance,
            'newbuilding' => $entrance->newbuilding,
            'actions' => $entrance->newbuilding->newbuildingComplex->activeActions,
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
        $form->section = $model->section;
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

                $successMessage = 'Квартира обновлена';

                // if 'section' field changed - move flat to another antrance or ignore 'section' field if entrance doesn't exist
                /* if($model->section !== (int)$form->section) {
                    $newEntrance = (new Entrance())
                        ->find()
                        ->where([ 'newbuilding_id' => $model->newbuilding_id ])
                        ->andWhere( [ 'number' => $form->section ] )
                        ->one();

                    if ($newEntrance instanceof Entrance) {
                        $form->entrance_id = $newEntrance->id;
                    } else {
                        $form->section = $model->section;
                        $successMessage = 'Квартира обновлена. Данные о подъезде не изменены, поскольку указанный подъезд не найден';
                    }
                } */
                $model->edit($form->attributes);
            } catch (\Exception $e) {
                return $this->redirectBackWhenException($e);
            }

            return $this->redirectWithSuccess(['admin/flat/index', 'entranceId' => $model->entrance_id], $successMessage);
        }
        
        return $this->render('update', [
            'model' => $form,
            'newbuilding' => $model->newbuilding,
            'entrance' => $model->entrance,
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
        $entranceId = $model->entrance->id;
        
        try {
            $model->delete();
        } catch (\Exception $e) {
            return $this->redirectBackWhenException($e);
        }

        return $this->redirectWithSuccess(['admin/flat/index', 'entranceId' => $entranceId], 'Квартира удалена');
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

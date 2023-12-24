<?php

namespace app\controllers\admin;

use app\components\exceptions\AppException;
use app\components\traits\CustomRedirects;
use app\models\Agency;
use app\models\form\AgencyForm;
use app\models\search\AgencySearch;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * AgencyController implements the CRUD actions for Agency model.
 */
class AgencyController extends Controller
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
                    ]
                ]
            ],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'index' => [
                'class' => 'app\components\actions\IndexWithSearch',
                'searchModelClass' => AgencySearch::classname(),
                'oldAdminPanel' => true,
            ],
        ];
    }

    /**
     * Creates a new Agency model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * 
     * @return mixed
     */
    public function actionCreate()
    {       
        $form = new AgencyForm();

        if (\Yii::$app->request->isPost && $form->load(\Yii::$app->request->post()) && $form->process()) {         
            try {
                $model = (new Agency())->fill($form->attributes);
                $model->save();
            } catch (\Exception $e) {
                return $this->redirectBackWhenException($e);
            }               

            return $this->redirectWithSuccess(['index'], 'Добавлено агенство недвижимости');
        }

        return $this->render('create', [
            'model' => $form,
        ]);
    }
    
    /**
     * Updates an existing Agency model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * 
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {        
        $model = $this->findModel($id); 
        $form = (new AgencyForm())->fill($model->attributes);
        $form->scenario = AgencyForm::SCENARIO_UPDATE;

        if (\Yii::$app->request->isPost && $form->load(\Yii::$app->request->post()) && $form->process()) {
            try {
                $model->fill($form->attributes, ['logo']);
                $model->logo = (!is_null($form->logo)) ? $form->logo : $model->logo;
                $model->save();
            } catch (\Exception $e) {
                return $this->redirectBackWhenException($e);
            }
            
            return $this->redirectWithSuccess(['index'], 'Агенство недвижимости обновлено');
        }

        return $this->render('update', [
            'model' => $form,
            'agency' => $model,
        ]);
    }
    
    /**
     * Deletes an existing Agency model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * 
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        try {
            if (count($model->contacts) || count($model->managers) || count($model->agents)) {
                throw new AppException('Агенство недвижимости не может быть удалено, так как имеет связанные с ней контакты или пользователей');
            }            
            $model->delete();            
        } catch (\Exception $e) {
            return $this->redirectBackWhenException($e);
        }
        
        return $this->redirectWithSuccess(['index'], 'Агенство недвижимости удалено');
    }
    
    /**
     * Finds the Agency model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * 
     * @param integer $id
     * @return Agency the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Agency::findOne($id)) === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }

        return $model;
    }
}

<?php

namespace app\controllers\admin;

use app\components\exceptions\AppException;
use app\components\traits\CustomRedirects;
use app\models\Bank;
use app\models\Flat;
use app\models\form\BankForm;
use app\models\search\BankSearch;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * BankController implements the CRUD actions for Bank model.
 */
class BankController extends Controller
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
    
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'index' => [
                'class' => 'app\components\actions\IndexWithSearch',
                'searchModelClass' => BankSearch::classname(),
            ],
        ];
    }

    /**
     * Creates a new Bank model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * 
     * @return mixed
     */
    public function actionCreate()
    {        
        $form = new BankForm();

        if (\Yii::$app->request->isPost && $form->load(\Yii::$app->request->post()) && $form->process()) {         
            try {
                (new Bank())->fill($form->attributes)->save();
            } catch (\Exception $e) {
                return $this->redirectBackWhenException($e);
            }               

            return $this->redirectWithSuccess(['index'], 'Добавлен банк');
        }

        return $this->render('create', [
            'model' => $form,
        ]);
    }

    /**
     * Updates an existing Bank model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * 
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {        
        $model = $this->findModel($id);
        $form = (new BankForm())->fill($model->attributes);
        $form->scenario = BankForm::SCENARIO_UPDATE;

        if (\Yii::$app->request->isPost && $form->load(\Yii::$app->request->post()) && $form->process()) {
            try {
                $model->fill($form->attributes, ['logo']);
                
                if ($form->attributes['is_logo_reset']) {
                    $model->logo = NULL;
                } else {
                    $model->logo = (!is_null($form->logo)) ? $form->logo : $model->logo;
                }
                
                $model->save();
            } catch (\Exception $e) {
                return $this->redirectBackWhenException($e);
            }
            
            return $this->redirectWithSuccess(['index'], 'Банк обновлен');
        }

        return $this->render('update', [
            'model' => $form,
            'bank' => $model,
        ]);
    }

    /**
     * Deletes an existing Bank model.
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
            if (count($model->newbuildingComplexes)) {
                throw new AppException('Банк не может быть удален, так как имеет аккредитованные объекты недвижимости');
            }
            $model->delete();
        } catch (\Exception $e) {
            return $this->redirectBackWhenException($e);
        }

        return $this->redirectWithSuccess(['index'], 'Банк удален');
    }

    /**
     * Finds the Bank model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * 
     * @param integer $id
     * @return Bank the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Bank::findOne($id)) === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }

        return $model;
    }
}

<?php

namespace app\controllers\admin;

use app\components\traits\CustomRedirects;
use app\models\service\Developer;
use app\models\form\DeveloperForm;
use app\models\form\ImportDataForm;
use app\models\search\DeveloperSearch;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * DeveloperController implements the CRUD actions for Developer model.
 */
class DeveloperController extends Controller
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
                'searchModelClass' => DeveloperSearch::classname(),
                'oldAdminPanel' => true,
            ],
        ];
    }

    /**
     * Creates a new Developer model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * 
     * @return mixed
     */
    public function actionCreate()
    {       
        $form = new DeveloperForm();
        $importForm = new ImportDataForm();

        if (\Yii::$app->request->isPost && 
            ($form->load(\Yii::$app->request->post())
            & $form->process() & $importForm->load(\Yii::$app->request->post())  & $importForm->process())
        ) {
            try {
                Developer::create($form->attributes, $importForm->attributes);
            } catch (\Exception $e) {
                return $this->redirectBackWhenException($e);
            }

            return $this->redirectWithSuccess(['index'], 'Добавлен застройщик');
        }

        return $this->render('create', [
            'model' => $form,
            'import' => $importForm,
        ]);
    }

    /**
     * Updates an existing Developer model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * 
     * @param integer $id developer ID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $developer = $this->findModel($id);        
        $form = (new DeveloperForm())->fill($developer->attributes);
        $form->scenario = DeveloperForm::SCENARIO_UPDATE;
        
        $importForm = new ImportDataForm();
        if (!is_null($developer->import)) {
            $importForm->fill($developer->import->attributes);
            $importForm->scenario = ImportDataForm::SCENARIO_UPDATE;
        }
        
        if (\Yii::$app->request->isPost && 
            ($form->load(\Yii::$app->request->post())
            & $form->process()& $importForm->load(\Yii::$app->request->post()) & $importForm->process())
        ) {
            try {
                $developer->edit($form->attributes, $importForm->attributes);
            } catch (\Exception $e) {
                return $this->redirectBackWhenException($e);
            }

            return $this->redirectWithSuccess(['index'], 'Застройщик обновлен');
        }

        return $this->render('update', [
            'developer' => $developer,
            'model' => $form,
            'import' => $importForm,
            'developerId' => $developer->id,
        ]);
    }

    /**
     * Deletes an existing Developer model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * 
     * @param integer $id developer ID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        try {
            $model->remove();
        } catch (\Exception $e) {
            return $this->redirectBackWhenException($e);
        }
        
        return $this->redirectWithSuccess(['index'], 'Застройщик удален');
    }
    
    /**
     * Finds the Developer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * 
     * @param integer $id developer ID
     * @return Developer the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Developer::findOne($id)) === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }

        return $model;
    }
}

<?php

namespace app\controllers\admin\contact;

use app\components\traits\CustomRedirects;
use app\models\Contact;
use app\models\NewbuildingComplex;
use app\models\form\ContactForm;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;

/**
 * ContactController implements the CRUD actions for Contact model.
 */
class NewbuildingComplexContactController extends Controller
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

    public function actionIndex($newbuildingComplexId) {
        if (($newbuildingComplex = NewbuildingComplex::findOne($newbuildingComplexId)) === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }

        $contactDataProvider = new ActiveDataProvider([
            'query' => $newbuildingComplex->getContacts(),
            'pagination' => false,
        ]);

        return $this->render('index', [
            'newbuildingComplex' => $newbuildingComplex,
            'contactDataProvider' => $contactDataProvider
        ]);
    }

    /**
     * Creates a new Contact model for newbuilding complex.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * 
     * @return mixed
     */
    public function actionCreate($newbuildingComplexId)
    {
        if (($newbuildingComplex = NewbuildingComplex::findOne($newbuildingComplexId)) === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }    
        
        $form = new ContactForm();
        $form->newbuilding_complex_id = $newbuildingComplex->id;
        
        if (\Yii::$app->request->isPost && $form->load(\Yii::$app->request->post()) && $form->process()) {         
            try {
                $model = (new Contact())->fill($form->attributes);
                $model->save();
                $newbuildingComplex->link('contacts', $model);                
            } catch (\Exception $e) {
                return $this->redirectBackWhenException($e);
            }               

            return $this->redirectWithSuccess(['index', 'newbuildingComplexId' => $newbuildingComplex->id], 'Добавлен контакт');
        }

        return $this->render('create', [
            'model' => $form,
            'newbuildingComplex' => $newbuildingComplex,
        ]);
    }

    /**
     * Updates an existing newbuilding complex's contact.
     * If update is successful, the browser will be redirected to the 'view' page.
     * 
     * @param integer $id newbuilding complex's contact ID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $form = (new ContactForm())->fill($model->attributes);
        $form->scenario = ContactForm::SCENARIO_UPDATE;
        
        if (\Yii::$app->request->isPost && $form->load(\Yii::$app->request->post()) && $form->process()) {            
            try {
                $model->fill($form->attributes, ['photo']);
                
                if ($form->attributes['is_phone_reset']) {
                    $model->photo = NULL;
                } else {
                    $model->photo = (!is_null($form->photo)) ? $form->photo : $model->photo;
                }
                
                $model->save();
            } catch (\Exception $e) {
                return $this->redirectBackWhenException($e);
            }

            return $this->redirectWithSuccess(['index', 'newbuildingComplexId' => $model->newbuildingComplex->id], 'Контакт обновлен');
        }

        return $this->render('update', [
            'model' => $form,
            'contact' => $model,
            'newbuildingComplex' => $model->newbuildingComplex,
        ]);
    }

    /**
     * Deletes an existing newbuilding complex's contact.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * 
     * @param integer $id newbuilding complex's contact ID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $newbuildingComplexId = $model->newbuildingComplex->id;
        
        try {
            $model->delete();
        } catch (\Exception $e) {
            return $this->redirectBackWhenException($e);
        }
        
        return $this->redirectWithSuccess(['index', 'newbuildingComplexId' => $newbuildingComplexId], 'Контакт удален');
    }

    /**
     * Finds the Contact model for newbuilding complex based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * 
     * @param integer $id newbuilding complex's contact ID
     * @return Contact the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Contact::findOne($id)) === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');            
        }
        
        return $model;
    }
}

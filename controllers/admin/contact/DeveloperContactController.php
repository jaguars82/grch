<?php

namespace app\controllers\admin\contact;

use app\components\traits\CustomRedirects;
use app\models\Contact;
use app\models\Developer;
use app\models\form\ContactForm;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * ContactController implements the CRUD actions for developer's contact.
 */
class DeveloperContactController extends Controller
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
    
    public function actionIndex($developerId) {
        if (($model = Developer::findOne($developerId)) === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }

        $contactDataProvider = new ActiveDataProvider([
            'query' => $model->getContacts(),
            'pagination' => false,
            'sort' => ['attributes' => ['id'], 'defaultOrder' => ['id' => SORT_DESC]],
        ]);

        return $this->render('index', [
            'model' => $model,
            'contactDataProvider' => $contactDataProvider,
        ]);
    }

    /**
     * Creates a new Contact model for developer.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * 
     * @return mixed
     */
    public function actionCreate($developerId)
    {
        if (($developer = Developer::findOne($developerId)) === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }    
        
        $form = new ContactForm();
        $form->developer_id = $developer->id;
        
        if (\Yii::$app->request->isPost && $form->load(\Yii::$app->request->post()) && $form->process()) {         
            try {
                $model = (new Contact())->fill($form->attributes);
                $model->save();
                $developer->link('contacts', $model);                
            } catch (Exception $e) {
                return $this->redirectBackWhenException($e);
            }

            return $this->redirectWithSuccess(['admin/contact/developer-contact/index', 'developerId' => $developer->id], 'Добавлен контакт');
        }

        return $this->render('create', [
            'model' => $form,
            'developer' => $developer,
        ]);
    }

    /**
     * Updates an existing developer's contact.
     * If update is successful, the browser will be redirected to the 'view' page.
     * 
     * @param integer $id developer's contact ID
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

            return $this->redirectWithSuccess(['admin/contact/developer-contact/index', 'developerId' => $model->developer->id], 'Контакт обновлен');
        }

        return $this->render('update', [
            'model' => $form,
            'contact' => $model,
            'developer' => $model->developer,
        ]);
    }

    /**
     * Deletes an existing developer's contact.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * 
     * @param integer $id developer's contact ID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $developerId = $model->developer->id;
        
        try {
            $model->delete();
        } catch (\Exception $e) {
            return $this->redirectBackWhenException($e);
        }
        
        return $this->redirectWithSuccess(['admin/contact/developer-contact/index', 'developerId' => $developerId], 'Контакт удален');
    }

    /**
     * Finds the Contact model for developer based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * 
     * @param integer $id developer's contact ID
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

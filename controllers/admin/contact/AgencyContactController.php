<?php

namespace app\controllers\admin\contact;

use app\components\traits\CustomRedirects;
use app\models\Agency;
use app\models\Contact;
use app\models\form\ContactForm;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * ContactController implements the CRUD actions for agency's contact.
 */
class AgencyContactController extends Controller
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
                        'roles' => ['admin', 'manager'],
                    ],
                ]
            ],
        ];
    }

    public function actionIndex($agencyId) {
        if (($model = Agency::findOne($agencyId)) === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }

        $contactDataProvider = new ActiveDataProvider([
            'query' => $model->getContacts(),
            'pagination' => false,
        ]);

        return $this->render('index', [
            'model' => $model,
            'contactDataProvider' => $contactDataProvider,
        ]);
    }

    /**
     * Creates a new Contact model for given agency.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * 
     * @return mixed
     */
    public function actionCreate($agencyId)
    {
        if (($agency = Agency::findOne($agencyId)) === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }
        
        $this->checkCurrentUser($agency);
        
        $form = new ContactForm();
        $form->agency_id = $agency->id;
        
        if (\Yii::$app->request->isPost && $form->load(\Yii::$app->request->post()) && $form->process()) {         
            try {
                $model = (new Contact())->fill($form->attributes);
                $model->save();
                $agency->link('contacts', $model);                
            } catch (Exception $e) {
                return $this->redirectBackWhenException($e);
            }               

            return $this->redirectWithSuccess(['admin/contact/agency-contact/index', 'agencyId' => $agency->id], 'Добавлен контакт');
        }

        return $this->render('create', [
            'model' => $form,
            'agency' => $agency,
        ]);
    }

    /**
     * Updates an existing agency's contact.
     * If update is successful, the browser will be redirected to the 'view' page.
     * 
     * @param integer $id agency's contact ID
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

            return $this->redirectWithSuccess(['admin/contact/agency-contact/index', 'agencyId' => $model->agency->id], 'Контакт обновлен');
        }
        
        return $this->render('update', [
            'model' => $form,
            'contact' => $model,
            'agency' => $model->agency,
        ]);
    }
    
    /**
     * Deletes an existing agency's contact.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * 
     * @param integer $id agency's contact ID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $agencyId = $model->agency->id;
        
        try {
            $model->delete();
        } catch (\Exception $e) {
            return $this->redirectBackWhenException($e);
        }
        
        return $this->redirectWithSuccess(['admin/contact/agency-contact/index', 'agencyId' => $agencyId], 'Контакт удален');
    }

    /**
     * Finds the Contact model for agency based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * 
     * @param integer $id agency's contact ID
     * @return Contact the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Contact::findOne($id)) === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');            
        }
        
        $this->checkCurrentUser($model->agency);
        
        return $model;
    }
    
    protected function checkCurrentUser($agency)
    {
        if (!\Yii::$app->user->can('admin') && !$agency->hasCurrentUser()) {
            throw new ForbiddenHttpException();
        }
    }
}

<?php

namespace app\controllers\user;

use app\components\traits\CustomRedirects;
use app\models\User;
use app\models\form\UserForm;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

class ProfileController extends Controller
{
    use CustomRedirects;

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['GET'],
                    'update' => ['GET', 'POST']
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'update'],
                        'roles' => ['admin', 'manager', 'agent'],
                    ],
                ]
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index', [
            'user' => \Yii::$app->user->identity
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $form = (new UserForm())->fill($model->attributes);
        $form->scenario = UserForm::SCENARIO_UPDATE;
        
        if (\Yii::$app->request->isPost && $form->load(\Yii::$app->request->post()) && $form->process()) {            
            try {
                $model->fill($form->attributes, ['photo']);
                
                if ($form->attributes['is_photo_reset']) {
                    $model->photo = NULL;
                } else {
                    $model->photo = (!is_null($form->photo)) ? $form->photo : $model->photo;
                }
                
                $model->save();
            } catch (\Exception $e) {
                return $this->redirectBackWhenException($e);
            }

            return $this->redirectWithSuccess(['user/profile/index'], 'Информация профиля обновлена');
        }

        return $this->render('update', [
            'model' => $form,
            'user' => $model,
            'redirectUrl' => 'index'
        ]);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * 
     * @param integer $id users's ID
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) === null) {
            throw new NotFoundHttpException('Данные профиля не найдены');            
        }
        
        return $model;
    }
}

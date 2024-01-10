<?php

namespace app\controllers\user;

use app\components\traits\CustomRedirects;
use app\components\SharedDataFilter;
use app\models\User;
use app\models\form\UserForm;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use tebe\inertia\web\Controller;
use yii\web\UploadedFile;

class ProfileController extends Controller
{
    use CustomRedirects;

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['GET', 'POST'],
                    'update' => ['GET', 'POST']
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'update'],
                        'roles' => ['admin', 'manager', 'agent', 'developer_repres'],
                    ],
                ]
            ],
            [
                'class' => SharedDataFilter::class
            ],
        ];
    }

    public function actionIndex()
    {
        if (\Yii::$app->request->isPost) {
            try {
                $userModel = User::findOne(\Yii::$app->user->identity->id);
                $userModel->passauth_enabled = 1;
                $userModel->password_hash = \Yii::$app->security->generatePasswordHash(\Yii::$app->request->post('password'));
                $userModel->save();
                $passSaved = true;

            } catch (\Exception $e) {
                return $this->redirectBackWhenException($e);
            }
        }

        $user = \Yii::$app->user->identity;

        return $this->inertia('User/Profile/Index', [
            'user' => $user,
            'passSaved' => isset($passSaved) ? $passSaved : false
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $form = (new UserForm())->fill($model->attributes);
        $form->scenario = UserForm::SCENARIO_UPDATE;
        
        if (\Yii::$app->request->isPost && $form->load(\Yii::$app->request->post(), '') && $form->process()) {      
            try {
                $model->fill($form->attributes, ['photo']);
                
                if ($form->attributes['is_photo_reset']) {
                    $model->photo = NULL;
                } else {
                    $model->photo = (!is_null($form->photo)) ? $form->photo : $model->photo;
                }
                
                $model->save();
            } catch (\Exception $e) {
                // return $this->redirectBackWhenException($e);
                return $this->inertia('User/Profile/Update', ['messageError' => !empty($e) ? $e : 'Ошибка при обновлении профиля']);
            }

            // return $this->redirectWithSuccess(['user/profile/index'], 'Информация профиля обновлена');
            return $this->inertia('User/Profile/Index', ['messageSuccess' => 'Информация профиля обновлена']);
        }

        return $this->inertia('User/Profile/Update', [
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

<?php

namespace app\components\actions\user;

use app\components\exceptions\AppException;
use app\models\Agency;
use app\models\User;
use app\models\form\UserForm;
use yii\base\Action;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * Action for creating agency's user
 */
class CreateAgencyUser extends Action
{
    public $message;
    public $isCheckCurrentUser = false;
    public $role;
    public $redirectUrl;
    public $redirectParameter;
     
    /**
     * Execute action
     * 
     * @param type $agencyId agency ID
     * @return Response
     * @throws NotFoundHttpException
     * @throws ForbiddenHttpException
     */
    public function run($agencyId)
    {
        if (($agency = Agency::findOne($agencyId)) === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }
        
        if ($this->isCheckCurrentUser && !\Yii::$app->user->can('admin') && !$agency->hasCurrentUser()) {
            throw new ForbiddenHttpException();
        }

        if ($agency->user_limit != 0 && count($agency->users) >= $agency->user_limit) {
            \Yii::$app->session->setFlash('error', "В данном агенстве недвижимости не может быть больше {$agency->user_limit} пользователей");
            return $this->controller->redirect(\Yii::$app->request->referrer);
        }
        
        $form = new UserForm();
        $form->agency_id = $agency->id;

        if (\Yii::$app->request->isPost && $form->load(\Yii::$app->request->post()) && $form->process()) {
            try { 
                $model = (new User())->fill($form->attributes);
                $model->save();
                $role = \Yii::$app->authManager->getRole($this->role);
                \Yii::$app->authManager->assign($role, $model->id);
            } catch (\Exception $e) {
                \Yii::$app->session->setFlash('error', ($e instanceof AppException) ? $e->getMessage() : 'Произошла ошибка. Обратитесь в службу поддержки');
                return $this->controller->redirect(\Yii::$app->request->referrer);
            }

            \Yii::$app->session->setFlash('success', $this->message);
            return $this->controller->redirect([$this->redirectUrl, $this->redirectParameter => $agency->id]);
        }

        return $this->controller->render('create', [
            'model' => $form,
            'agency'=> $agency,
            'redirectUrl' => $this->redirectUrl
        ]);
    }
}

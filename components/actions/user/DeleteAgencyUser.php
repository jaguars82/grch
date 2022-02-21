<?php

namespace app\components\actions\user;

use app\models\User;
use yii\base\Action;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * Action for deleting agency's user
 */
class DeleteAgencyUser extends Action
{
    public $message;
    public $isCheckCurrentUser = false;
    public $redirectUrl;
    public $redirectParameter;

    /**
     * Execute action
     * 
     * @param type $id agency user ID
     * @return Response
     * @throws NotFoundHttpException
     * @throws ForbiddenHttpException
     */
    public function run($id)
    {
        if (($model = User::findOne($id)) === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }
        
        if ($this->isCheckCurrentUser && !\Yii::$app->user->can('admin') && !$model->agency->hasCurrentUser()) {
            throw new ForbiddenHttpException();
        }
        
        try {
            \Yii::$app->authManager->revokeAll($model->id);
            $model->delete();
        } catch (\Exception $e) {
            \Yii::$app->session->setFlash('error', ($e instanceof AppException) ? $e->getMessage() : 'Произошла ошибка. Обратитесь в службу поддержки');
            return $this->controller->redirect(\Yii::$app->request->referrer);
        }
        
        \Yii::$app->session->setFlash('success', $this->message);
        return $this->controller->redirect([$this->redirectUrl, $this->redirectParameter => $model->agency->id]); //'agency/view'
    }
}

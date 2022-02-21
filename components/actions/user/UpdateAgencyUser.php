<?php

namespace app\components\actions\user;

use app\models\User;
use app\models\form\UserForm;
use yii\base\Action;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * Action for updating agency's user
 */
class UpdateAgencyUser extends Action
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
                \Yii::$app->session->setFlash('error', ($e instanceof AppException) ? $e->getMessage() : 'Произошла ошибка. Обратитесь в службу поддержки');
                return $this->controller->redirect(\Yii::$app->request->referrer);
            }

            \Yii::$app->session->setFlash('success', $this->message);
            return $this->controller->redirect([$this->redirectUrl, $this->redirectParameter => $model->agency->id]);
        }

        return $this->controller->render("update", [
            'model' => $form,
            'user' => $model,
            'redirectUrl' => $this->redirectUrl
        ]);
    }
}

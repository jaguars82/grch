<?php

namespace app\components\actions\user;

use app\models\User;
use yii\base\Action;

/**
 * Action for viewing agency's user
 */
class ViewAgencyUser extends Action
{
    public $view;
    
    /**
     * Execute action
     * 
     * @param type $id agency user ID
     * @return Response
     * @throws NotFoundHttpException
     */
    public function run($id)
    {
        if (($model = User::findOne($id)) === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }
        
        return $this->controller->render($this->view, [
            'model' => $model,
        ]);
    }
}

<?php

namespace app\controllers\user;

use app\components\traits\CustomRedirects;
use app\components\SharedDataFilter;
use app\models\User;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use tebe\inertia\web\Controller;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    use CustomRedirects;
    
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
                    'user-info' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['user-info'],
                        'roles' => ['@'],
                    ],
                ]
            ],
            [
                'class' => SharedDataFilter::class
            ],
        ];
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }

        return $model;
    }

    protected function findModelMinimized($id)
    {
        $model = User::find()
            ->select(['id', 'agency_id', 'developer_id', 'first_name', 'last_name', 'middle_name', 'phone', 'email', 'photo', 'last_login'])
            ->where(['id' => $id])
            ->one();

        if ($model === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }

        return $model;
    }

    /**
     * Gets information about a user
     * and sends a reply in JSON format
     */
    public function actionUserInfo()
    {
        $user = $this->findModelMinimized(\Yii::$app->request->post('id'));
        $data = ArrayHelper::toArray($user);
        $data['roleLabel'] = $user->roleLabel;

        // Add agency
        if (\Yii::$app->request->post('withAgency') && !empty($user->agency_id)) {
            $agency = $user->getAgency()
                ->select(['id', 'name', 'logo'])
                ->one();
            if (!empty($agency)) {
                $data['agency'] = ArrayHelper::toArray($agency);
            }
        }

        // Add developer
        if (\Yii::$app->request->post('withDeveloper') && !empty($user->agency_id)) {
            $developer = $user->getDeveloper()
                ->select(['id', 'name', 'logo'])
                ->one();
            if (!empty($developer)) {
                $data['developer'] = ArrayHelper::toArray($developer);
            }
        }

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        \Yii::$app->response->data = $data;
    }
}

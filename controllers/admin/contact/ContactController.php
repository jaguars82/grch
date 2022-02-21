<?php

namespace app\controllers\admin\contact;

use app\components\traits\CustomRedirects;
use app\models\Contact;
use app\models\Developer;
use app\models\NewbuildingComplex;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class ContactController extends Controller
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
                        'actions' => ['interaction'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['get-for-developer', 'get-for-newbuilding-complex'],
                        'roles' => ['@'],
                    ],
                ]
            ],
        ];
    }
    

    public function actionInteraction()
    {
        $developers = Developer::find()->whereNewbuildingComplexesExist()->orderBy(['id' => SORT_DESC])->all();
        
        return $this->render('/contact/interaction', [
            'developers' => $developers,
        ]);
    }
    
    public function actionGetForDeveloper($id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;        
        \Yii::$app->response->data = Contact::find()->forDeveloper($id)->orderBy(['contact.id' => SORT_DESC])->asArray()->all();
    }
    
    public function actionGetForNewbuildingComplex($id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        if (($newbuildingComplex = NewbuildingComplex::findOne($id)) === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
                
        $result = [['stages' => $newbuildingComplex->stages]];
        \Yii::$app->response->data = array_merge($result, Contact::find()->forNewbuildingComplex($id)->orderBy(['contact.id' => SORT_DESC])->asArray()->all());
    }
}
<?php

namespace app\controllers\contact;

use app\components\traits\CustomRedirects;
use app\models\Contact;
use app\models\Developer;
use app\models\NewbuildingComplex;
use app\models\search\AgencyContactSearch;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * ContactController implements the CRUD actions for Contact model.
 */
class ContactController extends Controller
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
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['get-for-developer', 'get-for-newbuilding-complex', 'interaction', 'index'],
                        'roles' => ['@'],
                    ],
                ]
            ],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'index' => [
                'class' => 'app\components\actions\IndexWithSearch',
                'searchModelClass' => AgencyContactSearch::classname(),
                'view' => '/contact/index'
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
                
        $result = [['algorithm' => $newbuildingComplex->algorithm, 'stages' => $newbuildingComplex->stages]];
        \Yii::$app->response->data = array_merge($result, Contact::find()->forNewbuildingComplex($id)->orderBy(['contact.id' => SORT_DESC])->asArray()->all());
    }

    /**
     * Finds the Contact model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
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

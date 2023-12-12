<?php

namespace app\controllers;

use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\components\SharedDataFilter;
 
class AdminController extends Controller 
{
    public $layout = 'admin';
    
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index'],
                        'roles' =>  ['admin'],
                    ],
                ]
            ],
            [
                'class' => SharedDataFilter::class
            ],
        ];
    }

    /**
     * Displays index page
     * 
     * @return $string
     */
    public function actionIndex() {
        return $this->render('index');
    }
}
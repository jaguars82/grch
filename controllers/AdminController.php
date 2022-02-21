<?php

namespace app\controllers;

use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

 
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
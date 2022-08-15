<?php

namespace app\controllers;

use app\models\Flat;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use tebe\inertia\web\Controller;

class ReservationController extends Controller
{
    public function behaviors()
    {
        return [ 
                'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    /*[
                        'allow' => true,
                        'actions' => ['create', 'update', 'delete'],
                        'roles' => ['admin'],
                    ],*/
                    [
                        'allow' => true,
                        'actions' => ['make'],
                        'roles' => ['@'],
                    ],
                ]
            ]
        ];
    }

    public function actionMake()
    {
        return $this->inertia('Reservation/Make', ['note' => 'Hello, there!!!']);
    }
}
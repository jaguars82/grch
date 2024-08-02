<?php

namespace app\controllers;

use app\components\SharedDataFilter;
use app\models\Lesson;
use app\models\LessonCategory;
use tebe\inertia\web\Controller;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class TutorialController extends Controller
{
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
                        'actions' => ['index', 'view'],
                        'roles' => ['admin', 'agent', 'manager'],
                    ],
                ]
            ],
            [
                'class' => SharedDataFilter::class
            ],
        ];
    }

    /**
     * Lists all Lesson models.
     * @return mixed
     */
    public function actionIndex()
    {
        $lessons = Lesson::find()->orderBy('sort_order ASC')->all();

        return $this->inertia('Tutorial/Index', [
            'lessons' => ArrayHelper::toArray($lessons),
        ]);
    }

    public function actionView($id)
    {
        $lesson = Lesson::findOne($id);

        return $this->inertia('Tutorial/View', [
            'lesson' => ArrayHelper::toArray($lesson),
        ]);
    }
}

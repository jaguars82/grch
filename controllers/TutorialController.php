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

        $lesson->video_source = $this->convertYouTubeUrl( $lesson->video_source);

        return $this->inertia('Tutorial/View', [
            'lesson' => ArrayHelper::toArray($lesson),
        ]);
    }

    private function convertYouTubeUrl($url) 
    {
        // Разбираем URL
        $parsedUrl = parse_url($url);
    
        // Проверяем наличие параметров
        if (!isset($parsedUrl['query'])) {
            return $url;
        }
    
        // Разбираем параметры URL
        parse_str($parsedUrl['query'], $queryParams);
    
        // Извлекаем идентификатор видео
        if (isset($queryParams['v'])) {
            $videoId = $queryParams['v'];
            return "https://www.youtube.com/embed/{$videoId}";
        } else {
            return $url;
        }
    }
}

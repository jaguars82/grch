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

        switch ($lesson->videohosting_type) {
            case Lesson::HOSTING_YOUTUBE:
                $lesson->video_source = $this->convertYouTubeUrl($lesson->video_source);
                break;
            case Lesson::HOSTING_RUTUBE:
                $lesson->video_source = $this->convertRutubeLink($lesson->video_source);
                break;
            case Lesson::HOSTING_VKVIDEO:
                $lesson->video_source = $this->convertVkVideoLink($lesson->video_source);
                break;
        }

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

    private function convertRutubeLink($url) {
        // Проверяем, что URL содержит rutube.ru/video/
        if (strpos($url, 'rutube.ru/video/') !== false) {
            // Извлекаем ID видео из ссылки
            $videoId = substr($url, strrpos($url, '/') + 1);
    
            // Формируем новую ссылку для встроенного видео
            return "https://rutube.ru/play/embed/{$videoId}/";
        } else {
            // Если URL не соответствует ожидаемому формату, возвращаем оригинальный URL
            return $url;
        }
    }

    private function convertVkVideoLink($url) {
        // Парсинг URL для извлечения параметров
        $parsedUrl = parse_url($url);
        
        // Получаем часть после "z=" из параметра "query"
        parse_str($parsedUrl['query'], $queryParams);
        
        if (isset($queryParams['z'])) {
            // Извлечение видео ID (формат видео-oid_id)
            if (preg_match('/video(-?\d+)_(\d+)/', $queryParams['z'], $matches)) {
                $oid = $matches[1];
                $id = $matches[2];
                
                // Формирование новой ссылки
                return "https://vk.com/video_ext.php?oid={$oid}&id={$id}&hd=2";
            }
        }
        
        // Если ссылка не соответствует формату, возвращаем оригинал
        return $url;
    }
}

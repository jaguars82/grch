<?php

namespace app\controllers\vidgets;

use tebe\inertia\web\Controller;
use app\components\SharedDataFilter;
use app\models\User;
use app\models\Messenger\Message;
use app\models\Messenger\MessageAttachment;
use app\models\Messenger\Thread;
use app\models\Messenger\Chat;
use app\models\Messenger\ChangesLog;
use app\models\Messenger\PublicChatParticipant;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

class MessengerController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'get-chat-by-url' => ['POST'],
                    'get-chat-messages' => ['POST'],
                    'get-chat' => ['POST'],
                    'create-message' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['get-chat-by-url', 'get-chat', 'get-chat-messages'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create-message'],
                        'roles' => ['@', '?'],
                    ],
                ]
            ],
        ];
    }

    /**
     * Creates a new message
     */
    public function actionCreateMessage()
    {
        $allErrors = [];

        $message = new Message();

        $message->load(\Yii::$app->request->post(), '');

        if (empty($message->chat_id)) {
            
            $chatId = $this->createChat(
                $message->author_id,
                \Yii::$app->request->post('chat_params'),
                $allErrors // передаём ссылку для сбора ошибок
            );

            if (!$chatId) {
                // Если создать чат не удалось — возвращаем ошибки без попытки сохранить сообщение
                \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return [
                    'success' => false,
                    'errors' => $allErrors,
                ];
            }

            $message->chat_id = $chatId;
        }

        if (!$message->save()) {
            $allErrors['message'] = $message->errors;
        }
    
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    
        return [
            'success' => empty($allErrors),
            'chatId' => $message->chat_id,
            'messageId' => $message->id ?? null,
            'created_at' => date('Y-m-d H:i:s'),
            'token' => \Yii::$app->request->post('token'),
            'errors' => $allErrors,
        ];
    }

    /**
     * Saves a new chat and returns it's ID
     */
    private function createChat($creatorId, array $chatParams = [], array &$errors = [])
    {
        $chat = new Chat();
        $chat->creator_id = $creatorId;
        $chat->attributes = $chatParams;
        
        if ($chat->save()) {
           return $chat->id; 
        }

        $errors['chat'] = $chat->errors;

        return false;
    }


    /**
     * Get a chat, attached to a specified URL
     */
    public function actionGetChatByUrl()
    {
        $urlParts = parse_url(\Yii::$app->request->post('url'));

        $needle = $urlParts['path'];

        if (count(\Yii::$app->request->post('params')) && !empty($urlParts['path'])) {
            parse_str($urlParts['query'], $urlParams);

            $paramIndex = 1;
            foreach (\Yii::$app->request->post('params') as $param) {
                if (!empty($urlParams[$param])) {
                    $glueSymbol = $paramIndex === 1 ? '?' : '&';
                    $needle .= "{$glueSymbol}{$param}={$urlParams[$param]}";
                    $paramIndex++;
                }
            }
        }

        $escapedNeedle = str_replace(['%', '_'], ['\%', '\_'], $needle);

        $queryChats = Chat::find()
            ->where(["is_url_attached" => 1])
            ->andWhere(["like", "url", "%{$escapedNeedle}%", false]);

        if (!\Yii::$app->request->post('is_url_admin')) {
            $queryChats->andWhere(['creator_id' => \Yii::$app->request->post('user_id')]);
            $urlAdmin = User::findOne(\Yii::$app->request->post('url_admin_id'));
        }

        $chats = $queryChats->all();

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        \Yii::$app->response->data = [
            'chats' => ArrayHelper::toArray($chats),
            'urlAdmin' => isset($urlAdmin) ? ArrayHelper::toArray($urlAdmin) : null,
        ];
    }

    /**
     * Get a chat by a given chat ID
     */
    public function actionGetChat()
    {
        $chat = Chat::findOne(\Yii::$app->request->post('id'));

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        \Yii::$app->response->data = ArrayHelper::toArray($chat);
    }

    /**
     * Get messages of a chat by a given chat ID
     */
    public function actionGetChatMessages()
    {
        $chat = Chat::findOne(\Yii::$app->request->post('id'));

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        \Yii::$app->response->data = ArrayHelper::toArray($chat->messages);
    }
}
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
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['get-chat-by-url'],
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

        $queryChats = Chat::find()
            ->where(["is_url_attached" => 1])
            ->andWhere(["like", "url", "%{$needle}%"]);

        if (!\Yii::$app->request->post('is_url_admin')) {
            $chats = $queryChats->andWhere(['creator_id' => \Yii::$app->request->post('user_id')]);
            $urlAdmin = User::findOne(\Yii::$app->request->post('url_admin_id'));
        }

        $chats = $queryChats->all();

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        \Yii::$app->response->data = [
            'chats' => ArrayHelper::toArray($chats),
            'urlAdmin' => isset($urlAdmin) ? ArrayHelper::toArray($urlAdmin) : null,
        ];
    }
}
<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';
$service_db = require __DIR__ . '/service_db.php';
$mailer = require __DIR__ . '/mailer.php';

$config = [
    'id' => 'basic',
    'name' => 'ГРЧ',
    'language' => 'ru-RU',
    'timeZone' => 'Europe/Moscow',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['inertia'],
    'layout' => 'new',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'inertia' => [
            'class' => 'tebe\inertia\Inertia'
        ],
        'request' => [
            'class' => 'tebe\inertia\web\Request',
            'cookieValidationKey' => 'QXFB9sWB1FlClPKQyGYTittb89LNcqMX',
            'enableCsrfValidation' => false,
            'enableCsrfCookie' => false,
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => ['/auth/login'],
            'on afterLogin' => function($event) {
                Yii::$app->user->identity->afterLogin($event);
            }
        ],
        'visitLogger' => [
            'class' => 'app\components\VisitLoggerComponent',
            'visitTimeout' => 600, // 10 minutes between repeated visits
        ],
        'locationHelper' => [
            'class' => 'app\components\LocationHelper',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'telegram' => function() use ($params) {
            return \app\services\TelegramSender::create($params['telegramBotKey']);
        },
        'mailer' => $mailer,
        'log' => [
            //'traceLevel' => YII_DEBUG ? 3 : 0,
            'traceLevel' => 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'agency/view/<id:\d+>' => 'agency/view',
                'agency/users/<id:\d+>' => 'agency/users',
                'agency/edit/<id:\d+>' => 'agency/edit',
                'agency/delete/<id:\d+>' => 'agency/delete',
                'offer/<path:index|update|delete|make|send-email|telegram|download-pdf>' => 'offer/<path>',
                'offer/<id>' => 'offer/view',
                'vidgets/messenger/create-message' => 'vidgets/messenger/create-message',
            ],
        ],
	    'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
	    'db' => $db,
        'messenger_db' => $service_db['messenger'],
        'formatter' => [
            'class' => 'app\components\Formatter',
            'numberFormatterSymbols' => [
                \NumberFormatter::CURRENCY_SYMBOL => '₽',
            ],
            'numberFormatterOptions' => [
                \NumberFormatter::MIN_FRACTION_DIGITS => 0,
            ],
            'defaultTimeZone' => 'Europe/Moscow',
        ],
    ],
    'controllerMap' => [
        'elfinder' => [
                'class' => 'mihaildev\elfinder\PathController',
                'access' => ['@'],
                'root' => [
                'path' => 'uploads/editor',
                'name' => 'Global'
            ],
        ]
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '::1', '95.32.*.*', '188.235.*.*'],
        //'allowedIPs' => ['*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '::1', '95.32.*.*'],
    ];
}

return $config;

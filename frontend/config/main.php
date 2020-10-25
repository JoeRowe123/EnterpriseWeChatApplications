<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-frontend',
    'defaultRoute'=>'index/get-user-info',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'modules' => [
        'h5' => [
            'class' => 'frontend\modules\h5\Module',
        ],
        'web' => [
            'class' => 'frontend\modules\web\Module',
        ],
    ],
    'components' => [
        'request' => [
            'class' => 'common\components\request\Request',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
            'csrfParam' => '_csrf-frontend',
            'enableCsrfValidation' => false,
        ],
        'response' => [
            'charset' => 'UTF-8',
            'class' => '\frontend\components\ApiResponse',
            'on beforeSend' => function ($event) {
                //@var $response \yii\web\Response
                $response = $event->sender;
                $response->getHeaders()->set('Access-Control-Allow-Origin', '*');
                $response->getHeaders()->set('Access-Control-Allow-Headers', 'accept, x-app-id, cache-control, token, content-type, Authorization');
                $response->getHeaders()->set('Access-Control-Allow-Methods', 'GET, POST, PUT,DELETE, OPTIONS');
            },
            'exceptActions' => [

            ]
        ],
        'user' => [
            'identityClass' => 'common\models\Member',
            'enableAutoLogin' => false,
            'enableSession' => false,
            'identityCookie' => ['name' => '_identity-api', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
            'timeout' => 60 * 60 * 24 * 180
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],

        'errorHandler' => [
            'errorAction' => 'index/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            "enableStrictParsing" => false,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
    ],
    'params' => $params,
];

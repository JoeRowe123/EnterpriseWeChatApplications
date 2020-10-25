<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [
        'admin' => [
            'class' => 'mdm\admin\Module',
            'layout' => '@backend/views/layouts/admin-layout',
        ],
    ],
    'aliases'             => [
        'zh/gii' => '@backend/zh-gii',
        'kn/images' => '@common/widgets/yii2-view-images',
        'kn/widget/file' => '@common/widgets/file-upload-widget',
        'kn/widget/json' => '@common/widgets/json-widget',
        'kn/content/view' => '@common/widgets/yii2-content-view',
    ],
    'components' => [
        //jquery v3.2.1 和 jQuery UI 1.11.4 版本冲突
        'assetManager' => [
            'assetMap' => [
                'jquery.js' => 'https://cdn.bootcss.com/jquery/2.1.4/jquery.js',
                'jquery.min.js' => 'https://cdn.bootcss.com/jquery/2.1.4/jquery.min.js',
            ],
            'hashCallback' => function ($path) {
                return md5($path);
            }
        ],
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'cache' => 'yii\redis\Cache'
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
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
    ],
    'as access' => [
        'class' => 'mdm\admin\components\AccessControl',
        'allowActions' => ['site/*', 'debug/*', 'user/auth',"file/*", 'api/*']
    ],
    'params' => $params,
];

<?php
$redisPass = '123456';
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=127.0.0.1;dbname=shop',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
        ],
        'redis'   => [
            'class'    => 'yii\redis\Connection',
            'hostname' => '127.0.0.1',
            'unixSocket' => false,
            'port' => 6379,
            'database' => 0,
            'password' => $redisPass,
        ],
        'session' => [
            'class' => 'yii\redis\Session',
            'redis' => [
                'hostname' => '127.0.0.1',
                'unixSocket' => false,
                'port'     => 6379,
                'database' => 0,
                'password' => $redisPass,
            ],
        ],
        'cache'   => [
            'class' => 'yii\redis\Cache',
            'redis' => [
                'hostname' => '127.0.0.1',
                'unixSocket' => false,
                'port'     => 6379,
                'database' => 0,
                'password' => $redisPass,
            ],
        ],
        'log'          => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'maxFileSize' => 1024 * 10 * 10,
                    'maxLogFiles' => 50,
                    'rotateByCopy' => false,
                    'except' => [
                        'yii\web\HttpException:401',
                    ],
                ],

            ]
        ],
    ],
];

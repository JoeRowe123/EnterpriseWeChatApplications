<?php
$redisPass = '123456';
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=49.234.109.252;dbname=qywechat',
            'username' => 'dxy',
            'password' => '123456',
            'charset' => 'utf8',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'redis'   => [
            'class'    => 'yii\redis\Connection',
            'hostname' => '127.0.0.1',
            'port'     => 6379,
            'database' => 0,
            "password" => $redisPass
        ],
        'session' => [
            'class' => 'yii\redis\Session',
            'redis' => [
                'hostname' => '127.0.0.1',
                'port'     => 6379,
                'database' => 0,
                "password" => $redisPass
            ],
        ],
        'cache'   => [
            'class' => 'yii\redis\Cache',
            'redis' => [
                'hostname' => '127.0.0.1',
                'unixSocket' => false,
                'port'     => 6379,
                'database' => 0,
                "password" => $redisPass
            ],
        ],
        'log'          => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['error', 'warning'],
                    'except' => [
                        'yii\web\HttpException:404',
                    ],
                ],
            ]
        ],
    ],
];

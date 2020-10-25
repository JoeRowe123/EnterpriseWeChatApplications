<?php

$config = [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '',
        ],
    ],
];

if (!YII_ENV_TEST) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'generators' => [
            'crud' => [
                'class' => 'zh\gii\generators\crud\Generator',
                'templates' => [
                    'zh' => '@backend/zh-gii/generators/crud/zh',
                ]
            ],
            'model' => [
                'class' => 'yii\gii\generators\model\Generator',
                'templates' => [
                    'zh' => '@backend/zh-gii/generators/model/zh',
                ]
            ],
        ]
    ];
}

return $config;

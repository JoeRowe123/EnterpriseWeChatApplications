<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'language'   => 'zh-CN',
    'timeZone'   => 'Asia/Chongqing',
    'components' => [
        'formatter' => [
            'class' => 'common\components\base\Formatter',
        ],
    ],

];

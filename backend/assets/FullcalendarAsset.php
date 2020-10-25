<?php

namespace backend\assets;

use yii\web\AssetBundle;

class FullcalendarAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/plugins/fullcalendar/fullcalendar.css',
        'css/plugins/fullcalendar/fullcalendar.print.css',
    ];
    public $js = [
        'js/plugins/fullcalendar/fullcalendar.min.js'
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
<?php

namespace backend\assets;

use yii\web\AssetBundle;

class VueCalendarAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/vue-calendar.css',
    ];
    public $js = [
        'js/plugins/dayjs.js',
        'js/vue-calendar.js'
    ];
    public $depends=[
        '\backend\assets\AdminAsset'
    ];
}
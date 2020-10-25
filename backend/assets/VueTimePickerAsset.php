<?php

namespace backend\assets;

use yii\web\AssetBundle;

class VueTimePickerAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/vue-time-picker.css',
    ];
    public $js = [
        'js/vue-time-picker.js'
    ];
    public $depends=[
        '\backend\assets\AdminAsset'
    ];
}
<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AdminAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/animate.css',
        'css/font-awesome.min.css',
        'css/style.css',
    ];
    public $js = [
        'js/plugins/layer/layer.min.js',
        'js/main.js',
        'https://cdn.jsdelivr.net/npm/vue/dist/vue.js',
    ];
    public $depends = [
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}

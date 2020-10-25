<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AdminLeftAssset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/animate.css',
        'css/font-awesome.min.css',
        'css/style.css',
    ];
    public $js = [
        'js/plugins/metisMenu/jquery.metisMenu.js',
        'js/plugins/slimscroll/jquery.slimscroll.min.js',
        'js/hplus.js?v=4.1.0',
        'js/contabs.js'
    ];
    public $depends = [
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}

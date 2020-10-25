<?php
namespace common\widgets\distpicker;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class DistPickerAsset extends AssetBundle
{
    public $js = [
        'https://cdn.bootcss.com/distpicker/2.0.5/distpicker.min.js',
    ];
    public $depends = [
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}


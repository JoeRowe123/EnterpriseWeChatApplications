<?php
namespace common\widgets\taginput;

class TagsinputAsset extends \yii\web\AssetBundle
{
    public $sourcePath =  __DIR__ . '/assets';

    public $css = [
        'bootstrap-tagsinput.css',
    ];
    public $js = [
        'bootstrap-tagsinput.min.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset'
    ];
}

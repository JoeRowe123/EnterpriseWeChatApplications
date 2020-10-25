<?php
namespace kn\images;


use yii\web\AssetBundle;

class ViewImagesAsset extends AssetBundle
{
    public $js = ['viewer.min.js'];
    public $css = ['viewer.min.css'];
    public $depends = ['yii\web\JqueryAsset'];
    public function init()
    {
        $this->sourcePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets';
    }
}
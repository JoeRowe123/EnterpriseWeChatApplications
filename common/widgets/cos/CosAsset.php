<?php

namespace common\widgets\cos;

use yii\web\AssetBundle;

class CosAsset extends AssetBundle
{
    public function init()
    {
        $this->sourcePath = __DIR__ . '/assets';
        $this->js  = ['cos.js'];
        $this->css = ['cos.css'];
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public $depends = [
        'yii\bootstrap\BootstrapAsset',
    ];
}
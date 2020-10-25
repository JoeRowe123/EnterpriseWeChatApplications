<?php

namespace backend\assets;

use yii\web\AssetBundle;

class FormAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [

    ];
    public $js = [

    ];
    public $depends=[
        'yii\widgets\ActiveFormAsset'
    ];
}
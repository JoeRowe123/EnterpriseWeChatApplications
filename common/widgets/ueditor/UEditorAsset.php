<?php
namespace common\widgets\ueditor;
use yii\web\AssetBundle;

/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2018/12/1
 * Time: 3:47 PM
 */
class UEditorAsset extends AssetBundle
{
    public $js = [
        'ueditor.config.js',
        'ueditor.all.min.js',
        'lang/zh-cn/zh-cn.js',
    ];

    public function init()
    {
        $this->sourcePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets';
    }
}
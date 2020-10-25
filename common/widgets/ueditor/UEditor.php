<?php

namespace common\widgets\ueditor;

use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;
use yii\widgets\InputWidget;

/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2018/12/1
 * Time: 3:35 PM
 */
class UEditor extends InputWidget
{
    //配置选项，参阅Ueditor官网文档(定制菜单等)
    public $clientOptions = [];

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $this->registerClientScript();
        if ($this->hasModel()) {
            return Html::activeTextarea($this->model, $this->attribute, ['id' => $this->id, 'name' => $this->name]);
        } else {
            return Html::textarea($this->id, $this->value, ['id' => $this->id, 'name' => $this->name]);
        }
    }

    /**
     * 注册客户端脚本
     */
    protected function registerClientScript()
    {
        UEditorAsset::register($this->view);
        $id = $this->id;
        $config = Json::encode($this->clientOptions);
        $script = "var ue = UE.getEditor('$id',$config)";
        $this->view->registerJs($script, View::POS_READY);
    }
}
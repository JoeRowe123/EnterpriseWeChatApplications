<?php

namespace common\widgets\cos;


use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;
use yii\widgets\InputWidget;


class CosWidget extends InputWidget
{

    const JS_CLASS_NAME = 'QiniuFileInput';

    /**
     * 上传地址配置
     * @var string
     */
    public $uploadUrl;

    /**
     * @var array
     */
    public $clientOptions = [];

    /**
     * @var string 文件默认图标路径
     */
    public $defaultFileIcon = "http://zhb.sinety.cn/img/file.png";

    /**
     * 默认客户端配置
     * @var array
     */
    protected $defaultClientOptions = [
        'max'    => 3,
        'size'   => 20480000 * 2,
        'accept' => 'image/jpeg,image/gif,image/png'
    ];


    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        if ($this->uploadUrl == null) {
            throw new InvalidConfigException('CosWidget::uploadUrl must be set');
        }
        parent::init();
        $this->registerAssetBundle();
        $this->registerPlugin();
    }

    public function run()
    {
        echo $this->getHtml();
    }

    /**
     * @return string
     * @throws InvalidConfigException
     */
    protected function getHtml()
    {
        global $elementIdIncrement;
        if ($this->hasModel()) {
            $el = $this->getElName() . ++$elementIdIncrement;;
            $id = html::getInputId($this->model, $this->attribute);
            $name = Html::getInputName($this->model, $this->attribute);
            $butName = isset($this->clientOptions['btnName']) ? $this->clientOptions['btnName'] : '请选择';
            $butClass  = isset($this->options['class']) ?$this->options['class'] : 'btn-success';
            $html = <<<HTML
                    <div id="$el">
                        <div class="zh-images" v-if="imageList.length > 0 || errMessage != ''">
                            <div class="err-box" v-show="errMessage != ''">
                                <span v-text="errMessage"></span><span class="close" @click="errMessage = ''">×</span>
                            </div>
                            <div v-for="(item,key) in imageList" class="zh-images-items">
                               <template v-if="!!item.name && (item.name.substring(item.name.lastIndexOf('.'), item.name.length) == '.jpg' || item.name.substring(item.name.lastIndexOf('.'), item.name.length) == '.png' || item.name.substring(item.name.lastIndexOf('.'), item.name.length) == '.gif' || item.name.substring(item.name.lastIndexOf('.'), item.name.length) == '.jpeg')">
                                   <img :src="item.name"><div class="zh-cover" @click="deleteImg(key)"><i class="glyphicon glyphicon-trash"></i></div>
                                </template>
                                <template v-else>
                                    <p style="text-align: center">{{item.name ? item.name.substring(item.name.lastIndexOf('/')+1, item.name.length + 1) : ''}}</p>
                                    <img src="$this->defaultFileIcon"><div class="zh-cover" @click="deleteImg(key)"><i class="glyphicon glyphicon-trash"></i></div>
                                </template>
                                
                            </div>
                        </div>
                        <div class="progress" v-if="progress > 0">
                            <div class="progress-bar" :style="{ width: progress+'%'}">{{progress}}%</div>
                        </div>
                        <div class="btn file-btn $butClass">
                            <span>$butName</span>
                            <template v-if="imageList.length > 0">
                            <input type="hidden" name="{$name}[]" v-model="img.name" v-for="img in imageList" v-if="imageList.length > 0" id="$id">
                            </template>
                            <template v-else>
                            <input type="hidden" id="$id">
                            </template>
                            <input type="file"  multiple="multiple" :accept="config.accept" @change="upload">
                        </div>
                    </div>
HTML;
            return $html;
        }
    }


    /**
     * Registers the asset bundle and locale
     */
    public function registerAssetBundle()
    {
        CosAsset::register($this->getView());
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    protected function getElName()
    {
        $str = $this->model->formName() . '-' . $this->attribute;

        return strpos($str, '[') !== false ? $this->model->formName() : $this->model->formName() . '-' . $this->attribute;
    }

    /**
     * Registers js and css
     * @throws InvalidConfigException
     */
    public function registerPlugin()
    {
        global $elementIdIncrementForJs;
        $config = array_merge( $this->defaultClientOptions,$this->clientOptions);
        $config['uploadUrl'] = $this->uploadUrl;
        $config['el'] = '#'.$this->getElName() . ++$elementIdIncrementForJs;;
        if (($value = Html::getAttributeValue($this->model, $this->attribute)) != null) {
            $config['imageList'] = (array) $value;
        }

        $js = Json::encode($config);
        $this->getView()->registerJs('new ' . self::JS_CLASS_NAME . "($js)", View::POS_END);
    }
}
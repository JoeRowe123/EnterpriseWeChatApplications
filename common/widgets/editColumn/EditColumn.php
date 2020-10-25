<?php

namespace common\widgets\editColumn;

use Yii;
use yii\grid\DataColumn;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;

class EditColumn extends DataColumn
{
    public $editUrl;

    protected $className;

    public $callBack = 'function (data) {
                    layer.msg(data.msg)
                }';

    public function init()
    {
        parent::init();
        $this->editUrl   = Url::to([$this->attribute]);
        $this->className = 'data-column-' . $this->attribute;
        $this->registerJs();
    }

    protected function registerJs()
    {
        $url = $this->editUrl;
        $csrf = Yii::$app->request->csrfToken;
        $csrfKey = Yii::$app->request->csrfParam;
        $className = $this->className;
        $attr = $this->attribute;
        $callBack = $this->callBack instanceof JsExpression ? $this->callBack : new JsExpression($this->callBack);
        $js = <<<JS
        $(".$className").blur(function(){
            var value = $(this).val();
            var key = $(this).data("key");
           $.ajax({
                url: "$url",
                type: "POST",
                data: {
                    "key" : key,
                    "value" : {
                        "$attr":value
                    },
                    "$csrfKey" : "$csrf"
                },
                success: $callBack,
                error: function (data) {
                     layer.msg("系统错误")
                }
            });
        });
 
JS;

        $this->grid->getView()->registerJs($js, View::POS_END);
    }


    public function renderDataCellContent($model, $key, $index)
    {
        $this->registerJs($key);
        $values = $this->value === null ? $model[$this->attribute] : call_user_func($this->value, $model);
        return "<input type='text'  value='$values' class='$this->className' data-key='$key'>";
    }


}
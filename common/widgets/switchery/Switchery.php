<?php

namespace common\widgets\switchery;

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2018/12/1
 * Time: 6:15 PM
 */
class Switchery extends \yh\switchery\Switchery
{

    public $action = 'update-status';
    /**
     * @param $model
     * @param $key
     * @param $index
     *
     * @return string
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        $this->registerScript();

        return Html::input('checkbox', null, null, [
            'class' => $this->inputClassName,
            'checked' => $model[$this->attribute] == $this->statusOpen,
            'id' => $this->attribute,
            'data-key' => $key,
        ]);
    }

    /**
     * Register js
     */
    protected function registerScript()
    {
        $url = Url::toRoute($this->action);
        $callBack = $this->callBack instanceof JsExpression ? $this->callBack : new JsExpression($this->callBack);
        $attribute = $this->attribute;

        $js = <<<JS
 $(document).on('change','#$attribute',function(){
    var status = $(this).prop('checked') === false ? '$this->statusClose' : '$this->statusOpen';
    $.get('$url',{key:$(this).data('key'),status:status},$callBack,'json');
});
JS;
        $this->grid->getView()->registerJs($js);
    }
}
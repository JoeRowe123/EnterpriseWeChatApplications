<?php

namespace kn\content\view;

use yii\bootstrap\Modal;
use yii\grid\DataColumn;
use yii\helpers\Html;
use yii\web\View;

/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2018/12/1
 * Time: 6:07 PM
 */
class ContentView extends DataColumn
{

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->grid->getView()->on(View::EVENT_BEGIN_BODY, function () {

            Modal::begin([
                'header' => '<h2>详情查看</h2>',
                'id'     => 'kn-modal',
                'size'   => Modal::SIZE_LARGE
            ]);

            echo '<div class="content" style="overflow: auto"></div>';

            Modal::end();
        });
        $this->hashPluginOptions();
    }


    public function renderDataCellContent($model, $key, $index)
    {
        $values = $this->value === null ? (array)$model[$this->attribute] : call_user_func($this->value, $model);
        return Html::button('', ['class' => 'btn btn-outline btn-default btn-xs glyphicon glyphicon-zoom-in btn-content', 'data-content' => $values]);
    }


    protected function hashPluginOptions()
    {
        $js = <<<JS
        $(".btn-content").click(function(){
            $("#kn-modal").modal("show").find(".content").html($(this).data("content"))
        });
JS;
        $this->grid->getView()->registerJs($js);
    }
}
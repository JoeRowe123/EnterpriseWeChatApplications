<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Activity */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => '活动助手', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<ul class="nav nav-tabs" role="tablist" id="nav">
    <li>
        <a href="<?=\yii\helpers\Url::toRoute(['view', 'id' => $id])?>">活动详情</a>
    </li>
    <li class="active">
        <a href="<?=\yii\helpers\Url::toRoute(['statistics', 'id' => $id])?>">报名统计</a>
    </li>
</ul>
<div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="base">
        <div class="article-index col-sm-12" style="margin-top: 15px;">
            <ul class="nav nav-tabs" role="tablist" id="nav">
                <li  class="<?=$status == 1 ? 'active' : ''?>">
                    <a href="<?=\yii\helpers\Url::toRoute(['statistics', 'id' => $id, 'status' => 1])?>">已报名</a>
                </li>
                <li class="<?=$status == 0 ? 'active' : ''?>">
                    <a href="<?=\yii\helpers\Url::toRoute(['statistics', 'id' => $id, 'status' => 0])?>">未报名</a>
                </li>
            </ul>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="base">
                    <div class="article-index col-sm-12" style="margin-top: 15px;">
                        <p>
                            <?= Html::a('导出选中', "javascript:void(0);", ['class' => 'btn btn-info gridview']) ?>
                            <?= Html::a('导出全部', ["batch-export-apply-info-all", "status" => $status, 'id' => $id], ['class' => 'btn btn-default']) ?>
                        </p>


                        <div class="ibox ibox-content">
                            <br>
                            <?= GridView::widget([
                                'dataProvider' => $dataProvider,
                                'tableOptions' => ['class' => 'table table-striped'],
                                'options' => ['class' => 'grid-view', 'style' => 'overflow:auto', 'id' => 'grid'],
                                //'filterModel' => $searchModel,
                                'columns' => [
                                    [
                                        'class' => \yii\grid\CheckboxColumn::className(),
                                        'name' => 'id',  //设置每行数据的复选框属性
                                    ],
                                    "username",
                                    [
                                        "attribute" => "apply_status",
                                        'visible' => $status != 0,
                                        "value" => function($model) {
                                            return \common\models\ActivityReadObject::$applyStatus[$model->apply_status];
                                        }
                                    ],
                                    [
                                        'attribute' => 'department_name',
                                        'value' => function ($m) {
                                            return implode(",", array_column($m->department_name, 'name'));
                                        }
                                    ],
                                    "position",
                                    "phone",
                                    [
                                        'attribute' => 'apply_options',
                                        'visible' => $status != 0,
                                        "format" => "raw",
                                        "value" => function($model) {
                                            $str = "";
                                            if($model->apply_options) {
                                                $str .= "<ul>";
                                                foreach ($model->apply_options as $k => $item) {
                                                    $num = $k+1;
                                                    if(!is_array($item['item_options'])) {
                                                        $str .= "<li style='list-style-type:none; '>{$num}、{$item['item_title']}:{$item['item_options']}</li>";
                                                    } else {
                                                        $val = implode(",", $item['item_options']);
                                                        $str .= "<li style='list-style-type:none; '>{$num}、{$item['item_title']}:{$val}</li>";
                                                    }
                                                }
                                                $str .= "</ul>";
                                            }
                                            return $str;
                                        }
                                    ],
                                    [
                                        'attribute' => 'apply_at',
                                        'visible' => $status != 0,
                                        'value' => function ($m) {
                                            return $m->apply_at ? date('Y-m-d H:i:s', $m->apply_at) : '';
                                        }
                                    ],
                                    [
                                        'class' => 'yii\grid\ActionColumn',
                                        'visible' => $status == 0,
                                        'header' => '操作',
                                        'headerOptions' => ['width' => '150px'],
                                        'template' => '{remind}',
                                        'buttons' => [
                                            'remind' => function ($url, $model, $key) {
                                                return Html::a('提醒报名', "javascript:void(0)", ['class' => 'btn btn-outline btn-info btn-xs tx-read', 'data-id' => $model->id]);
                                            }
                                        ],
                                    ],
                                ],
                            ]); ?>
                        </div>
                    </div>
                </div>

            </div>
    </div>

<?php
$url = \yii\helpers\Url::to(['batch-export-apply-info']);
$txUrl = \yii\helpers\Url::to(['remind']);
$js  = <<<JS
$(".gridview").on("click", function () {
    var arr = $("#grid").yiiGridView("getSelectedRows");
    if(arr.length==0){
        layer.msg("请至少选择一个报名人信息");
        return;
    }
    window.location.href = "$url"+"?ids="+arr+"&id="+$id+"&status="+$status;
});
$(".tx-read").on("click", function() {
    var id = $(this).data("id");
    $.get("$txUrl", {id:id}, function(data) {
        layer.msg(data.msg, {time:1500})
    });
})
JS;
$this->registerJs($js);
?>
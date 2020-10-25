<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ArticleCommentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title                   = '阅读情况';
$this->params['breadcrumbs'][] = ['label' => '返回', 'url' => $type == \common\models\Article::TYPE_TYKX ? ['index'] : ['list']];
$this->params['breadcrumbs'][] = $this->title;
?>
    <ul class="nav nav-tabs" role="tablist" id="nav">
        <li  class="<?=$status == 1 ? 'active' : ''?>">
            <a href="<?=\yii\helpers\Url::toRoute(['read-info', 'id' => $id, 'status' => 1])?>">已阅读(<?=$endCount?>)</a>
        </li>
        <li class="<?=$status == 0 ? 'active' : ''?>">
            <a href="<?=\yii\helpers\Url::toRoute(['read-info', 'id' => $id, 'status' => 0])?>">未阅读(<?=$count?>)</a>
        </li>
    </ul>
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="base">
            <div class="article-index col-sm-12" style="margin-top: 15px;">
                <p>
                    <?= Html::a('导出选中', "javascript:void(0);", ['class' => 'btn btn-info gridview']) ?>
                    <?= Html::a('导出全部', ["export-all", "status" => $status, 'id' => $id], ['class' => 'btn btn-default']) ?>
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
                                'attribute' => 'department_name',
                                'value' => function ($m) {
                                    return implode(",", array_column($m->department_name, 'name'));
                                }
                            ],
                            "position",
                            "phone",
                            [
                                'attribute' => 'read_time',
                                'value' => function ($m) {
                                    return $m->read_time ? date('Y-m-d H:i:s', $m->read_time) : '';
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
                                        if($model->article->reading_time <= time() && $model->article->status == \common\models\Article::STATUS_ACTIVE) {
                                            return Html::a('提醒阅读', "javascript:void(0)", ['class' => 'btn btn-outline btn-info btn-xs tx-read', 'data-id' => $model->id]);
                                        }
                                    }
                                ],
                            ],
                        ],
                    ]); ?>
                </div>
            </div>
        </div>


<?php
$url = \yii\helpers\Url::to(['export']);
$txUrl = \yii\helpers\Url::to(['remind']);
$js  = <<<JS
$(".gridview").on("click", function () {
    var arr = $("#grid").yiiGridView("getSelectedRows");
    if(arr.length==0){
        layer.msg("请至少选择一个用户信息");
        return;
    }
    window.location.href = "$url"+"?ids="+arr;
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
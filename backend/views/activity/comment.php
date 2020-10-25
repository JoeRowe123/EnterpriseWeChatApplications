<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ActivityCommentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = '评论列表';
$this->params['breadcrumbs'][] = ['label' => '返回', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

    <div class="article-index col-sm-12" style="margin-top: 15px;">
        <p>
            <?= Html::a('批量删除', "javascript:void(0);", ['class' => 'btn btn-danger gridview']) ?>
        </p>
        <div class="ibox ibox-content">
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
                        'attribute' => 'content',
                        'value' => function ($m) {
                            if($m->del == 1) {
                                return $m->content;
                            } else {
                                return '该评论已被删除';
                            }
                        }
                    ],
                    [
                        'attribute' => 'created_at',
                        'value' => function ($m) {
                            return date('Y-m-d H:i:s', $m->created_at);
                        }
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header' => '操作',
                        'headerOptions' => ['width' => '150px'],
                        'template' => ' {comment-delete}',
                        'buttons' => [
                            'comment-delete' => function ($url, $model, $key) {
                                if($model->del == 1) {
                                    return Html::a('删除', $url, ['data-confirm' => '你确定要删除吗?', 'data-method' => 'POST', 'class' => 'btn btn-outline btn-danger btn-xs']);
                                }
                            }
                        ],
                    ],
                ],
            ]); ?>
        </div>
    </div>

<?php
$url = \yii\helpers\Url::to(['batch-delete-comment']);
$js  = <<<JS
$(".gridview").on("click", function () {
    var arr = $("#grid").yiiGridView("getSelectedRows");
    if(arr.length==0){
        layer.msg("请至少选择一个评论");
        return;
    }
    window.location.href = "$url"+"?ids="+arr+"&id="+$id;
});
JS;
$this->registerJs($js);
?>
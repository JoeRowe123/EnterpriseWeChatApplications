<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ActivitySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '活动助手';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="activity-index col-sm-12" style="margin-top: 15px;">
        <p>
            <?= Html::a("新建活动", ['create'], ['class' => 'btn btn-success']) ?>
            <?= Html::a('批量删除', "javascript:void(0);", ['class' => 'btn btn-danger gridview']) ?>
        </p>
        <div class="ibox ibox-content">
            <?php echo $this->render('_search', ['model' => $searchModel]); ?>


            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions' => ['class' => 'table table-striped'],
                'options' => ['class' => 'grid-view', 'style' => 'overflow:auto', 'id' => 'grid'],
                //'filterModel' => $searchModel,
                'columns' => [
                    //['class' => 'yii\grid\SerialColumn'],
                    [
                        'class' => \yii\grid\CheckboxColumn::className(),
                        'name' => 'id',  //设置每行数据的复选框属性
                    ],
                    "title",
                    [
                        'attribute' => 'view_num',
                        'format' => 'raw',
                        'value' => function ($m) {
                            return Html::a($m->view_num . '/' . $m->total_num, ['read-info', 'id' => $m->id]);
                        }
                    ],
                    [
                        'attribute' => 'apply_num',
                        'format' => 'raw',
                        'value' => function ($m) {
                            if($m->limit_person_num != 0) {
                                return Html::a($m->apply_num . '/' . $m->limit_person_num, ['statistics', 'id' => $m->id]);
                            } else {
                                return Html::a($m->apply_num . '/' . $m->total_num, ['statistics', 'id' => $m->id]);
                            }
                        }
                    ],
                    [
                        'attribute' => 'comment_num',
                        'format' => 'raw',
                        'value' => function ($m) {
                            return Html::a($m->comment_num, ['comment-info', 'id' => $m->id]);
                        }
                    ],
                    [
                        'attribute' => 'like_num',
                        'format' => 'raw',
                        'value' => function ($m) {
                            return Html::a($m->like_num, ['like-info', 'id' => $m->id]);
                        }
                    ],
                    [
                        'attribute' => 'created_at',
                        'value' => function ($m) {
                            return date('Y-m-d H:i:s', $m->created_at);
                        }
                    ],
                    'author_name',
                    [
                        'attribute' => 'status',
                        'value' => function ($m) {
                            return \common\models\Activity::$status[$m->status];
                        }
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header' => '操作',
                        'headerOptions' => ['width' => '150px'],
                        'template' => '{update} {view} {delete}',
                        'buttons' => [
                            'update' => function ($url, $model, $key) {
                                if($model->status != \common\models\Activity::STATUS_END) {
                                    return Html::a('编辑', $url, ['class' => 'btn btn-outline btn-info btn-xs', 'style' => 'margin-top:5px;']);
                                }
                            },
                            'view' => function ($url, $model, $key) {
                                return Html::a('查看详情', $url, ['class' => 'btn btn-outline btn-info btn-xs', 'style' => 'margin-top:5px;']);
                            },
                            'delete' => function ($url, $model, $key) {
                                return Html::a('删除', $url, ['data-confirm' => '你确定要删除吗?', 'data-method' => 'POST', 'class' => 'btn btn-outline btn-danger btn-xs', 'style' => 'margin-top:5px;']);
                            }
                        ],
                    ],
                ],
            ]); ?>
        </div>
    </div>

<?php
$url = \yii\helpers\Url::to(['batch-delete']);
$js  = <<<JS
$(".gridview").on("click", function () {
    var arr = $("#grid").yiiGridView("getSelectedRows");
    if(arr.length==0){
        layer.msg("请至少选择一个活动");
        return;
    }
    window.location.href = "$url"+"?ids="+arr;
});
JS;
$this->registerJs($js);
?>
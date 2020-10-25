<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\VoteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '投票管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vote-index col-sm-12" style="margin-top: 15px;">
        <p>
            <?= Html::a('新建投票', ['create'], ['class' => 'btn btn-success']) ?>
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
                        'label' => '开始时间',
                        'value' => function ($m) {
                            return date("Y-m-d H:i:s", $m->start_time);
                        }
                    ],
                    [
                        'label' => '结束时间',
                        'value' => function ($m) {
                            return date("Y-m-d H:i:s", $m->end_time);
                        }
                    ],
                    [
                        'attribute' => 'status',
                        'value' => function ($m) {
                            return \common\models\Vote::$status[$m->status];
                        }
                    ],
                    [
                        'attribute' => 'vote_type',
                        'value' => function ($m) {
                            return $m->vote_type == 1 ? '实名投票' : '匿名投票';
                        }
                    ],
                    'author_name',

                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header' => '操作',
                        'headerOptions' => ['width' => '150px'],
                        'template' => '{update} {view} {delete} ',
                        'buttons' => [
                            'update' => function ($url, $model, $key) {
                                if($model->status != \common\models\Vote::STATUS_END) {
                                    return Html::a('编辑', $url, ['class' => 'btn btn-outline btn-info btn-xs','style' => 'margin-top:5px;']);
                                }
                            },
                            'view' => function ($url, $model, $key) {
                                return Html::a('查看详情', $url, ['class' => 'btn btn-outline btn-info btn-xs','style' => 'margin-top:5px;']);
                            },
                            'delete' => function ($url, $model, $key) {
                                return Html::a('删除', $url, ['data-confirm' => '你确定要删除吗?', 'data-method' => 'POST', 'class' => 'btn btn-outline btn-danger btn-xs','style' => 'margin-top:5px;']);
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
        layer.msg("请至少选择一个投票");
        return;
    }
    window.location.href = "$url"+"?ids="+arr;
});
JS;
$this->registerJs($js);
?>
<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ExaminationPaperSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '试卷列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="examination-paper-index col-sm-12" style="margin-top: 15px;">
        <p>
            <?= Html::a('新建试卷', ['create'], ['class' => 'btn btn-success']) ?>
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
                    'sn',
                    "name",
                    [
                        'label' => '考试时间',
                        'value' => function ($m) {
                            return date("Y-m-d H:i:s", $m->start_time). ' - ' . date("Y-m-d H:i:s", $m->end_time);
                        }
                    ],
                    [
                        'attribute' => 'duration_time',
                        'value' => function ($m) {
                            return $m->duration_time. '分';
                        }
                    ],
                    [
                        'attribute' => 'total_grade',
                        'value' => function ($m) {
                            return $m->total_grade. '分';
                        }
                    ],
                    [
                        'attribute' => 'pass_mark',
                        'value' => function ($m) {
                            return $m->pass_mark. '分';
                        }
                    ],
                    [
                        'attribute' => 'status',
                        'value' => function ($m) {
                            return \common\models\ExaminationPaper::$status[$m->status];
                        }
                    ],
                    'author_name',
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
                        'template' => ' {update} {delete} {view}',
                        'buttons' => [
                            'update' => function ($url, $model, $key) {
                                if($model->status == \common\models\ExaminationPaper::STATUS_NOT_START || $model->status == \common\models\ExaminationPaper::STATUS_DRAFT) {
                                    return Html::a('编辑', ["create", 'type' => 'edit', 'id' => $model->id], ['class' => 'btn btn-outline btn-info btn-xs', 'style' => 'margin-top:5px;']);
                                }
                            },
                            'view' => function ($url, $model, $key) {
                                return Html::a('查看统计', $url, ['class' => 'btn btn-outline btn-info btn-xs', 'style' => 'margin-top:5px;']);
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
        layer.msg("请至少选择一个试卷");
        return;
    }
    window.location.href = "$url"+"?ids="+arr;
});
JS;
$this->registerJs($js);
?>
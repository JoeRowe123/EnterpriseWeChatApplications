<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ArticleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$bu                            = $type == \common\models\Article::TYPE_TYKX ? '新建酷讯' : '新建学堂';
$ti                            = $type == \common\models\Article::TYPE_TYKX ? '天一酷讯' : '微学堂';
$this->title                   = $ti;
$this->params['breadcrumbs'][] = $this->title;
?>

    <div class="article-index col-sm-12" style="margin-top: 15px;">
        <p>
            <?= Html::a($bu, ['create?type=' . $type], ['class' => 'btn btn-success']) ?>
            <?= Html::a('批量删除', "javascript:void(0);", ['class' => 'btn btn-danger gridview']) ?>
        </p>
        <div class="ibox ibox-content">
            <?php echo $this->render('_search', ['model' => $searchModel, 'type' => $type]); ?>


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
                    'id',
                    "title",
                    [
                        'attribute' => 'view_number',
                        'format' => 'raw',
                        'value' => function ($m) {
                            return Html::a($m->view_number . '/' . $m->total_number, ['read-info', 'id' => $m->id]);
                        }
                    ],
                    [
                        'attribute' => 'is_secrecy',
                        'value' => function ($m) {
                            return $m->is_secrecy == 1 ? '是' : '否';
                        }
                    ],
                    [
                        'attribute' => 'comment_number',
                        'format' => 'raw',
                        'value' => function ($m) {
                            return Html::a($m->comment_number, ['comment-info', 'id' => $m->id]);
                        }
                    ],
                    [
                        'attribute' => 'like_number',
                        'format' => 'raw',
                        'value' => function ($m) {
                            return Html::a($m->like_number, ['like-info', 'id' => $m->id]);
                        }
                    ],
                    [
                        'attribute' => 'updated_at',
                        'value' => function ($m) {
                            return date('Y-m-d H:i:s', $m->updated_at);
                        }
                    ],
                    'author_name',
                    [
                        'attribute' => 'status',
                        'value' => function ($m) {
                            return \common\models\Article::$status[$m->status];
                        }
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header' => '操作',
                        'headerOptions' => ['width' => '150px'],
                        'template' => '{update} {delete} {copy}',
                        'buttons' => [
                            'update' => function ($url, $model, $key) {
                                return Html::a('修改', $url, ['class' => 'btn btn-outline btn-info btn-xs']);
                            },
                            'delete' => function ($url, $model, $key) {
                                return Html::a('删除', $url, ['data-confirm' => '你确定要删除吗?', 'data-method' => 'POST', 'class' => 'btn btn-outline btn-danger btn-xs']);
                            },
                            'copy' => function ($url, $model, $key) {
                                return Html::a('复制', \yii\helpers\Url::to(["create", "type" => $model->type, "id" => $model->id]), ['data-confirm' => '你确定要复制吗?', 'data-method' => 'POST', 'class' => 'btn btn-outline btn-default btn-xs']);
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
        layer.msg("请至少选择一个文章");
        return;
    }
    window.location.href = "$url"+"?ids="+arr+"&type="+$type;
});
JS;
$this->registerJs($js);
?>
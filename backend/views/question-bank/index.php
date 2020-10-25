<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\QuestionBankSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '题库列表';
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="article-index col-sm-12" style="margin-top: 15px;">
        <p>
            <?= Html::a('新建题库', ['create'], ['class' => 'btn btn-success']) ?>
        </p>
        <div class="ibox ibox-content">
            <?php echo $this->render('_search', ['model' => $searchModel]); ?>


            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions' => ['class' => 'table table-striped'],
                //'filterModel' => $searchModel,
                'columns' => [
                    //['class' => 'yii\grid\SerialColumn'],
                    'sn',
                    "name",
                    "single_num",
                    "multiple_num",
                    "judge_num",
                    "gap_filling_num",
                    "total_num",
                    "author_name",
                    [
                        'attribute' => 'created_at',
                        'value' => function ($m) {
                            return date('Y-m-d H:i:s', $m->created_at);
                        }
                    ],
                    [
                        'attribute' => 'status',
                        'value' => function ($m) {
                            return \common\models\QuestionBank::$status[$m->status];
                        }
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header' => '操作',
                        'headerOptions' => ['width' => '150px'],
                        'template' => '{view} {add} {update} {delete}',
                        'buttons' => [
                            'view' => function ($url, $model, $key) {
                                return Html::a('查看题目', $url, ['class' => 'btn btn-outline btn-info btn-xs']);
                            },
                            'add' => function ($url, $model, $key) {
                                return Html::a('添加题目', $url, ['class' => 'btn btn-outline btn-info btn-xs']);
                            },
                            'update' => function ($url, $model, $key) {
                                return Html::a('编辑题库名称', $url, ['class' => 'btn btn-outline btn-info btn-xs', 'style' => 'margin-top:5px;']);
                            },
                            'delete' => function ($url, $model, $key) {
                                if($model->status == \common\models\QuestionBank::STATUS_ACTIVE) {
                                    return Html::a('删除', $url, ['data-confirm' => '你确定要删除吗?', 'data-method' => 'POST', 'class' => 'btn btn-outline btn-danger btn-xs', 'style' => 'margin-top:5px;']);
                                }
                            },
                            'start' => function ($url, $model, $key) {
                                if($model->status == \common\models\QuestionBank::STATUS_DISABLED) {
                                    return Html::a('启用', $url, ['data-confirm' => '你确定要启用吗?', 'data-method' => 'POST', 'class' => 'btn btn-outline btn-success btn-xs', 'style' => 'margin-top:5px;']);
                                }
                            }
                        ],
                    ],
                ],
            ]); ?>
        </div>
    </div>

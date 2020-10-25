<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\TaskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '任务列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="task-index col-sm-12" style="margin-top: 15px;">
    <p>
        <?= Html::a('新建同步任务', ['create'], ['class' => 'btn btn-success']) ?>（<span style="color: red">同步企业微信组织架构及成员信息</span>）

    </p>
    <div class="ibox ibox-content">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'tableOptions' => ['class' => 'table table-striped'],
            //'filterModel' => $searchModel,
            'columns' => [
                //['class' => 'yii\grid\SerialColumn'],
                'sn',
                "name",
                [
                    'attribute' => 'status',
                    'value' => function ($m) {
                        return \common\models\Task::$status[$m->status];
                    }
                ],
                "msg",
                [
                    'attribute' => 'created_at',
                    'value' => function ($m) {
                        return date('Y-m-d H:i:s', $m->created_at);
                    }
                ]
            ],
        ]); ?>
    </div>
</div>

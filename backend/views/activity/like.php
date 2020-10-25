<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ActivityLikeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title                   = '点赞列表';
$this->params['breadcrumbs'][] = ['label' => '返回', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="article-index col-sm-12" style="margin-top: 15px;">
    <div class="ibox ibox-content">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'tableOptions' => ['class' => 'table table-striped'],
            //'filterModel' => $searchModel,
            'columns' => [
                "username",
                [
                    'attribute' => 'created_at',
                    'value' => function ($m) {
                        return date('Y-m-d H:i:s', $m->created_at);
                    }
                ],
            ],
        ]); ?>
    </div>
</div>

<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ArticleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = '管理员列表';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="article-index col-sm-12" style="margin-top: 15px;">
    <p>
        <?= Html::a('添加管理员', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <div class="ibox ibox-content">
<!--        --><?php // echo $this->render('_search', ['model' => $searchModel]); ?>


        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'tableOptions' => ['class' => 'table table-striped'],
            //'filterModel' => $searchModel,
            'columns' => [
                //['class' => 'yii\grid\SerialColumn'],

                'id',
                'username',
                'email',
               [
                   "attribute" =>  'created_at',
                   "value" => function ($m) {
                        return date("Y-m-d H:i:s", $m->created_at);
                   }
               ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'header' => '操作',
                    'headerOptions' => ['width'=>'150px'],
                    'template' => '{delete}',
                    'buttons' => [
                        'update' => function($url, $model, $key){
                            return Html::a('修改密码', $url,['class' =>'btn btn-outline btn-info btn-xs']);
                        },
                        'delete' => function($url, $model, $key){
                            return Html::a('删除', $url,['data-confirm' => '你确定要删除吗?','data-method' => 'POST','class' =>'btn btn-outline btn-danger btn-xs']);
                        }
                    ],
                ],
            ],
        ]); ?>
    </div>
</div>
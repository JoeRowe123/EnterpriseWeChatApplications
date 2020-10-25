<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ArticleCategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = '分类列表';
$this->params['breadcrumbs'][] = $this->title;

?>
<style>
    .table, th {
        text-align: left;
    }
</style>

<div class="article-category-index col-sm-12" style="margin-top: 15px;">
    <p>
        <?= Html::a('新建分类', ['create?type='.$type], ['class' => 'btn btn-success']) ?>
        <?= Html::a('批量删除', "javascript:void(0);", ['class' => 'btn btn-danger gridview']) ?>
    </p>
    <div class="ibox ibox-content float-e-margins">
        <?php echo $this->render('_search', ['model' => $searchModel, 'type' => $type]); ?>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'tableOptions' => ['class' => 'table table-striped'],
            'options' => ['class' => 'grid-view', 'style' => 'overflow:auto', 'id' => 'grid'],
//            'filterModel' => $searchModel,
            'columns' => [
                [
                    'class' => \yii\grid\CheckboxColumn::className(),
                    'name' => 'id',  //设置每行数据的复选框属性
                ],
                ['class' => 'yii\grid\SerialColumn', 'header' => '序号'],
                [
                    'attribute' => 'name',
                    'label' => '分类名称',
                ],
                [
                    'attribute' => 'status',
                    'label' => '状态',
                    'class' => \common\widgets\switchery\Switchery::class,
                    'action' => 'update-status',
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'header' => '操作',
                    'headerOptions' => ['width'=>'150px'],
                    'template' => '{update} {delete}',
                    'buttons' => [
                        'update' => function($url, $model, $key){
                            return Html::a('修改', $url,['class' =>'btn btn-outline btn-info btn-xs']);
                        },
                        'delete' => function($url, $model, $key){
                            return Html::a('删除', $url,['data-confirm' => '你确定要删除吗?','data-method' => 'POST','class' =>'btn btn-outline btn-danger btn-xs']);
                        },
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
        layer.msg("请至少选择一个分类");
        return;
    }
    window.location.href = "$url"+"?ids="+arr+"&type="+$type;
});
JS;
$this->registerJs($js);
?>
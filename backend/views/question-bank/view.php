<?php

use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\QuestionBankItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '查看题目';
$this->params['breadcrumbs'][] = ['label' => '返回', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="article-index col-sm-12" style="margin-top: 15px;">
    <p>
        <?= Html::a('批量导入', "javascript:void(0);", ['class' => 'btn btn-success batch-import']) ?>
        <?= Html::a('下载模板', "javascript:void(0);", ['class' => 'btn btn-danger gridview']) ?>
        <?= Html::a('添加题目', \yii\helpers\Url::to(['add', 'id' => $id]), ['class' => 'btn btn-success']) ?>
    </p>
    <div class="ibox ibox-content">

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
                [
                    'attribute' => 'title',
                    "options" => ["width" => "600px"],
                    'value' => function ($m) {
                        return $m['title'];
                    }
                ],
                [
                    'attribute' => 'type',
                    'value' => function ($m) {
                        return \common\models\QuestionBankItem::$type[$m['type']];
                    }
                ],
                "grade",
                [
                    'attribute' => 'answer',
                    "options" => ["width" => "400px"],
                    'value' => function ($m) {
                        if(is_array($m['answer'])) {
                            $str = implode(",", $m['answer']);
                        } else {
                            $str = $m['answer'];
                        }
                        return $str;
                    }
                ],
                "sort",
                [
                    'attribute' => 'status',
                    'value' => function ($m) {
                        return \common\models\QuestionBankItem::$status[$m['status']];
                    }
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'header' => '操作',
                    'headerOptions' => ['width' => '150px'],
                    'template' => '{update-item} {delete-item} {start-item}',
                    'buttons' => [
                        'update-item' => function ($url, $model, $key) {
                            return Html::a('编辑', $url, ['class' => 'btn btn-outline btn-info btn-xs', 'style' => 'margin-top:5px;']);
                        },
                        'delete-item' => function ($url, $model, $key) {
                            if($model['status'] == \common\models\QuestionBankItem::STATUS_ACTIVE) {
                                return Html::a('禁用', $url, ['data-confirm' => '你确定要禁用吗?', 'data-method' => 'POST', 'class' => 'btn btn-outline btn-danger btn-xs', 'style' => 'margin-top:5px;']);
                            }
                        },
                        'start-item' => function ($url, $model, $key) {
                            if($model['status'] == \common\models\QuestionBankItem::STATUS_DISABLED) {
                                return Html::a('启用', $url, ['data-confirm' => '你确定要启用吗?', 'data-method' => 'POST', 'class' => 'btn btn-outline btn-danger btn-xs', 'style' => 'margin-top:5px;']);
                            }
                        }
                    ],
                ],
            ],
        ]); ?>
    </div>
</div>
<?php
$importUrl = \yii\helpers\Url::to(["batch-import", 'id' => $id]);
Modal::begin([
    'header' => '<h2>批量导入</h2>',
    'id' => 'modal'
]);

echo "<div id='content'></div>";

Modal::end();


$url = \yii\helpers\Url::to(['download']);
$js  = <<<JS
$(".gridview").on("click", function () {
    var arr = $("#grid").yiiGridView("getSelectedRows");
    if(arr.length==0){
        layer.msg("请至少选择一个题目");
        return;
    }
    window.location.href = "$url"+"?ids="+arr+"&bank_id="+"$id";
});

 $('.batch-import').click(function() {
        $('#modal').modal('show').find("#content").load("$importUrl");
 });
JS;
$this->registerJs($js);
?>
<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ArticleSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="article-search search-wrap">

    <?php $form = ActiveForm::begin([
        'action' => $type == \common\models\Article::TYPE_TYKX ? ['index'] : ['list'],
        'method' => 'get',
        'options' => ['class' => 'form-inline'],
        'fieldConfig' => [
            'options' => ['class' => 'form-group'],
            'template' => "{input}"
        ],
    ]); ?>

    <?= $form->field($model, 'title') ->textInput(['placeholder'=>'请输入标题'])?>

    <?= $form->field($model, 'created_at')->widget(\kartik\datetime\DateTimePicker::classname(), [
        'options' => ['placeholder' => '请选择创建时间', 'autocomplete' => 'off'],
        'pluginOptions' => [
            'autoclose' => true,
            'todayHighlight' => true,
            "minView"=> "month",
            'format' => 'yyyy-mm-dd'
        ]
    ]); ?>

    <div class="form-group">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<hr />

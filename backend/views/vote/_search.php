<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\VoteSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="vote-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => ['class' => 'form-inline'],
        'fieldConfig' => [
            'options' => ['class' => 'form-group'],
            'template' => "{input}"
        ],
    ]); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true, 'placeholder' => '请输入投票标题']) ?>

    <?= $form->field($model, 'author_name')->textInput(['maxlength' => true, 'placeholder' => '请输入发起人']) ?>

    投票状态： <?= $form->field($model, 'status') ->dropDownList(\common\models\Vote::$status, ['prompt'=>'全部'])?>

    投票方式： <?= $form->field($model, 'vote_type') ->dropDownList([1 => '实名投票', 2 => '匿名投票'], ['prompt'=>'全部'])?>

    <?= $form->field($model, 'start_time')->widget(\kartik\datetime\DateTimePicker::classname(), [
        'options' => ['placeholder' => '请选择投票开始时间', 'autocomplete' => 'off'],
        'pluginOptions' => [
            'autoclose' => true,
            'todayHighlight' => true,
            "minView"=> "month",
            'format' => 'yyyy-mm-dd'
        ]
    ]); ?>
    <?= $form->field($model, 'end_time')->widget(\kartik\datetime\DateTimePicker::classname(), [
        'options' => ['placeholder' => '请选择投票结束时间', 'autocomplete' => 'off'],
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

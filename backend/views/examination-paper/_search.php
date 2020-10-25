<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ExaminationPaperSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="examination-paper-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => ['class' => 'form-inline'],
        'fieldConfig' => [
            'options' => ['class' => 'form-group'],
            'template' => "{input}"
        ],
    ]); ?>

    试卷状态： <?= $form->field($model, 'status') ->dropDownList(\common\models\ExaminationPaper::$status, ['prompt'=>'全部'])?>

    考试时间：
    <?= $form->field($model, 'start_time')->widget(\kartik\datetime\DateTimePicker::classname(), [
        'options' => ['placeholder' => '请选择考试开始时间', 'autocomplete' => 'off'],
        'pluginOptions' => [
            'autoclose' => true,
            'todayHighlight' => true,
            "minView"=> "month",
            'format' => 'yyyy-mm-dd'
        ]
    ]); ?>
    <?= $form->field($model, 'end_time')->widget(\kartik\datetime\DateTimePicker::classname(), [
        'options' => ['placeholder' => '请选择考试结束时间', 'autocomplete' => 'off'],
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

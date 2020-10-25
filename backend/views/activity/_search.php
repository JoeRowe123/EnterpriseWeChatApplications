<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ActivitySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="activity-search search-wrap">

        <?php $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
            'options' => ['class' => 'form-inline'],
            'fieldConfig' => [
                'options' => ['class' => 'form-group'],
                'template' => "{input}",
            ],
        ]); ?>

        活动状态：<?= $form->field($model, 'status') ->dropDownList(\common\models\Activity::$status, ['prompt'=>'全部'])?>

        <?= $form->field($model, 'title') ->textInput(['placeholder'=>'请输入活动标题'])?>

        <?= $form->field($model, 'author_name') ->textInput(['placeholder'=>'请输入发起人'])?>


        <?= $form->field($model, 'created_at')->widget(\kartik\datetime\DateTimePicker::classname(), [
            'options' => ['placeholder' => '请选择发布时间', 'autocomplete' => 'off'],
            'pluginOptions' => [
                'autoclose' => true,
                'todayHighlight' => true,
                "minView"=> "month",
                'format' => 'yyyy-mm-dd',
            ]
        ]); ?>

        <?= $form->field($model, 'start_time')->widget(\kartik\datetime\DateTimePicker::classname(), [
            'options' => ['placeholder' => '请选择活动开始时间', 'autocomplete' => 'off'],
            'pluginOptions' => [
                'autoclose' => true,
                'todayHighlight' => true,
                "minView"=> "month",
                'format' => 'yyyy-mm-dd'
            ]
        ]); ?>
        <?= $form->field($model, 'end_time')->widget(\kartik\datetime\DateTimePicker::classname(), [
            'options' => ['placeholder' => '请选择活动结束时间', 'autocomplete' => 'off'],
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

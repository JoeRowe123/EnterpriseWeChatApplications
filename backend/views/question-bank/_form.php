<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\QuestionBank */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="question-bank-form">

    <?php $form = ActiveForm::begin([
        'options' => [
            'class' => 'form-horizontal',
            "id" => "question-bank-form",
            "onkeypress" => "if(event.keyCode==13)return false;"],
        'fieldConfig' => [
            'options' => ['class' => 'form-group'],
            'template' => "{label}\n<div class=\"col-sm-8\">{input}\n<span class=\"help-block m-b-none\">{error}</span></div>",
            'labelOptions' => ['class' => 'col-sm-2 control-label'],
        ]
    ]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => '请输入题库名称']) ?>


    <div class="form-group">
        <div class="col-sm-4 col-sm-offset-2">
            <?= Html::a('返回', ['index'], ['class' => 'btn btn-info']) ?>
            <?= Html::submitButton('添加', ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

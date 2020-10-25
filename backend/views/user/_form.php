<?php

use common\models\ArticleCategory;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin([
        'options'     => [
            'class'      => 'form-horizontal',
            "id"         => "user-form",
            "onkeypress" => "if(event.keyCode==13)return false;"
        ],
        'fieldConfig' => [
            'options'      => ['class' => 'form-group'],
            'template'     => "{label}\n<div class=\"col-sm-8\">{input}\n<span class=\"help-block m-b-none\">{error}</span></div>",
            'labelOptions' => ['class' => 'col-sm-2 control-label'],
        ]
    ]); ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true, 'disabled' => $id ? true : false]) ?>

    <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>


    <div class="form-group">
        <div class="col-sm-4 col-sm-offset-2">
            <?= Html::submitButton(!$id ? '创建' : '更新', ['class' => !$id ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

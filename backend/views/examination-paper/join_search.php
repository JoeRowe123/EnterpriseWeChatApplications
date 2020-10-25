<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ExaminationUsersSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="examination-paper-search">

    <?php $form = ActiveForm::begin([
        'action' => ['view', 'id' => $id],
        'method' => 'get',
        'options' => ['class' => 'form-inline'],
        'fieldConfig' => [
            'options' => ['class' => 'form-group'],
            'template' => "{input}"
        ],
    ]); ?>

    状态： <?= $form->field($model, 'status') ->dropDownList(\common\models\ExaminationUsers::$status, ['prompt'=>'全部'])?>

    <?= $form->field($model, 'username') ->textInput(['placeholder'=>'请输入姓名'])?>

    <?= $form->field($model, 'department_name') ->textInput(['placeholder'=>'请输入部门'])?>

    <div class="form-group">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<hr />

<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\QuestionBankSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="article-search search-wrap">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => ['class' => 'form-inline'],
        'fieldConfig' => [
            'options' => ['class' => 'form-group'],
            'template' => "{input}"
        ],
    ]); ?>

    <?= $form->field($model, 'name') ->textInput(['placeholder'=>'请输入题库名称'])?>

    <?= $form->field($model, 'author_name') ->textInput(['placeholder'=>'请输入创建人'])?>

    <?= $form->field($model, 'created_at')->widget(\kartik\datetime\DateTimePicker::classname(), [
        'options' => ['placeholder' => '请选择创建时间'],
        'pluginOptions' => [
            'autoclose' => true,
            'todayHighlight' => true,
            "minView"=> "month",
            'format' => 'yyyy-mm-dd',
        ]
    ]); ?>

    <div class="form-group">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<hr />

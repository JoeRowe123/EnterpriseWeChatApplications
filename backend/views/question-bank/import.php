<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Article */
/* @var $form yii\widgets\ActiveForm */
?>

    <div class="import-form">

        <?php $form = ActiveForm::begin([
            'options' => ['class' => 'form-horizontal', 'id' => 'import-form'],
            'fieldConfig' => [
                'options' => ['class' => 'form-group'],
                'template' => "{label}\n<div class=\"col-sm-8\">{input}\n<span class=\"help-block m-b-none\">{error}</span></div>",
                'labelOptions' => ['class' => 'col-sm-2 control-label'],
            ]
        ]); ?>

        <?= $form->field($model, 'batch_import')->fileInput(['maxlength' => true, 'accept' => '.xls,.xlsx']) ?>
        <sapn style="color: #ff0000;margin-left: 12px;"> 注：1、上传之前请先清除excel中的格式、公式、链接等；<br/>
            <sapn style="color: #ff0000;margin-left: 37px;"> 2、点击导入后，请勿刷新、关闭页面，处理完成以后页面会自动跳转提示；   </sapn>
        </sapn>

        <div class="form-group">
            <div class="col-sm-4 col-sm-offset-2" style="margin-top: 15px;">
                <?= Html::button('确认导入', ['class' => 'btn btn-success import', "onclick" => "afterSubmit()"]) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

<?php
$js = <<<JS

  function afterSubmit(e) {
      $(".import").attr("disabled", true);
      $(".import").text("导入中");
      $("#import-form").submit();
  }

JS;
$this->registerJs($js, 2);

?>
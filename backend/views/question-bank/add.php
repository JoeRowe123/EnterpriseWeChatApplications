<?php

use wbraganca\dynamicform\DynamicFormWidget;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\QuestionBank */
?>

        <div class="question-bank-form">

            <?php $form = ActiveForm::begin([
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
                'id' => 'dynamic-form',
                'options' => [
                    'class' => 'form-horizontal',
                    "onkeypress" => "if(event.keyCode==13)return false;"],
                'fieldConfig' => [
                    'options' => ['class' => 'form-group'],
                    'template' => "{label}\n<div class=\"col-sm-8\">{input}\n<span class=\"help-block m-b-none\">{error}</span></div>",
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                ]
            ]); ?>
            <?= $form->field($model, 'type')->dropDownList(\common\models\QuestionBankItem::$type, ['id' => 'type', 'disabled' => $model->isNewRecord ? false : true]) ?>

            <?= $form->field($model, 'sort')->textInput(['maxlength' => true, 'placeholder' => '请输入题目排序']) ?>

            <?= $form->field($model, 'grade')->textInput(['maxlength' => true, 'placeholder' => '请输入正整数']) ?>

            <?= $form->field($model, 'title')->textarea(['row' => 3, 'maxlength' => true, 'placeholder' => '不超过500个汉字']) ?>

            <div class="options"
                 style="display: <?= (!$model->isNewRecord && $model->type == \common\models\QuestionBankItem::TYPE_GAP_FILLING) || (!$model->isNewRecord && $model->type == \common\models\QuestionBankItem::TYPE_JUDGE) ? 'none' : 'block' ?>">
                <div class="form-group field-fgj-area_id_2 required">
                    <label class="col-sm-2 control-label" for="fgj-area_id_2">选项</label>
                    <div class="col-sm-8">
                        <?php DynamicFormWidget::begin([
                            'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                            'widgetBody' => '.container-items', // required: css class selector
                            'widgetItem' => '.item', // required: css class
                            'limit' => 20, // the maximum times, an element can be cloned (default 999)
                            'min' => 1, // 0 or 1 (default 1)
                            'insertButton' => '.add-item', // css class
                            'deleteButton' => '.remove-item', // css class
                            'model' => $modelAttr[0],
                            'formId' => 'dynamic-form',
                            'formFields' => [
                                'name',
                                'type'
                            ],
                        ]); ?>
                        <div class="panel panel-default ">
                            <div class="panel-heading">
                                <h4>
                                    <i class="glyphicon glyphicon-envelope"></i>
                                    <button type="button" class="add-item btn btn-success btn-sm pull-right"><i
                                                class="glyphicon glyphicon-plus"></i></button>
                                </h4>
                            </div>
                            <div class="panel-body">
                                <div class="container-items"><!-- widgetBody -->
                                    <?php foreach ($modelAttr as $i => $item): ?>
                                        <div class="item panel panel-default"><!-- widgetItem -->
                                            <div class="panel-heading">
                                                <span class="panel-title-address">选项<?= \common\helpers\StringHelper::numToLetter(($i)) ?></span>
                                                <h3 class="panel-title pull-left"></h3>
                                                <div class="pull-right">
                                                    <button type="button" class="remove-item btn btn-danger btn-xs">
                                                        <i class="glyphicon glyphicon-minus"></i></button>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                            <div class="panel-body">
                                                <?php
                                                // necessary for update action.
                                                //                                                    if (!$item->isNewRecord) {
                                                //                                                        echo Html::activeHiddenInput($item, "[{$i}]id");
                                                //                                                    }
                                                ?>

                                                <?= $form->field($item, "[{$i}]name")->textarea(['maxlength' => true]) ?>
                                                <?= $form->field($item, "[{$i}]type")->hiddenInput(['maxlength' => true, 'class' => 'optionType', 'value' => 1])->label("") ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div><!-- .panel -->
                        <?php DynamicFormWidget::end(); ?>
                    </div>
                </div>
            </div>

            <div class="answer1"
                 style="display: <?= !$model->isNewRecord && $model->type == \common\models\QuestionBankItem::TYPE_GAP_FILLING ? 'block' : 'none' ?>">
                <?= $form->field($model, 'answer')->textarea(['row' => 6, 'placeholder' => '请输入正确答案']) ?>
            </div>

            <div class="answer2"
                 style="display: <?= !$model->isNewRecord && $model->type == \common\models\QuestionBankItem::TYPE_JUDGE ? 'block' : 'none' ?>">
                <?= $form->field($model, 'answer2')->radioList(['对' => '对', '错' => '错']) ?>

            </div>

            <div class="answer3"
                 style="display: <?= !$model->isNewRecord && $model->type == \common\models\QuestionBankItem::TYPE_MULTIPLE ? 'block' : 'none' ?>">
                <?= $form->field($model, 'answer3')->checkboxList(["A" => "A"]) ?>
            </div>

            <div class="answer4"
                 style="display: <?= $model->isNewRecord || $model->type == \common\models\QuestionBankItem::TYPE_SINGLE ? 'block' : 'none' ?>">
                <?= $form->field($model, 'answer4')->radioList(["A" => "A"]) ?>
            </div>


            <div class="form-group">
                <div class="col-sm-4 col-sm-offset-2">
                    <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
                </div>
            </div>

            <?php ActiveForm::end(); ?>

        </div>

<?php
$answer4 = $model->isNewRecord ? '' : $model->answer4;
$answer3 = $model->isNewRecord ? [] : json_decode($model->answer3, true) ?? [];
$arrStr = "[";
$count = count($answer3);
for ($i = 0; $i<$count; $i++) {
    if($count - $i == 1) {
        $arrStr .= "'".$answer3[$i]."'";
    } else {
        $arrStr .= "'".$answer3[$i]."', ";
    }
}
$arrStr .= "]";
$js                            = <<<JS
    $(function() {
        function in_array(search,array){
            for(var i in array){
                if(array[i]==search){
                    return true;
                }
            }
            return false;
        }
        var arr = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
        function addOptions() {
           $(".dynamicform_wrapper .panel-title-address").each(function(index) {
                var name = arr[index];
                $(this).html("选项" + (name));
                var radioHtml = $("#questionbankitem-answer4").html();
                var checkHtml = $("#questionbankitem-answer3").html();
                var answer4 = "$answer4" === name ? 'checked' : '';
                var answer3 = in_array(name, "$arrStr") ? 'checked' : '';
                if(parseInt($("#type").val()) === 1) {
                    radioHtml += "<div class='radio'><label><input type='radio' name='QuestionBankItem[answer4]' "+answer4+" value='"+name+"'> "+name+"</label></div>";
                   $("#questionbankitem-answer4").html(radioHtml) ;
                } else if(parseInt($("#type").val()) === 2) {
                       checkHtml += "<div class='checkbox'><label><input type='checkbox' name='QuestionBankItem[answer3][]' "+answer3+" value='"+name+"'> "+name+"</label></div>";
                   $("#questionbankitem-answer3").html(checkHtml) ;
                }
            });
        }
        
        function refresh()
        {
            $("#questionbankitem-answer4").children().remove();
            $("#questionbankitem-answer3").children().remove();
            addOptions();
        }
         refresh();
       $(".dynamicform_wrapper").on("afterInsert", function(e, item) {
            refresh();
            $(".optionType").attr("value",$(".optionType").val());
        });
    
        $(".dynamicform_wrapper").on("afterDelete", function(e) {
            refresh();
        });
  
    
     $("#type").change(function() {
         refresh();
     if($(this).val() == 1) {
         $(".answer4").css("display", "block");
         $(".answer3").css("display", "none");
         $(".answer2").css("display", "none");
         $(".answer1").css("display", "none");
         $(".options").css("display", "block");
         $(".optionType").attr("value","1");
        
     } else if($(this).val() == 2) {
        $(".answer3").css("display", "block");
         $(".answer4").css("display", "none");
         $(".answer2").css("display", "none");
         $(".answer1").css("display", "none");
         $(".options").css("display", "block");
         $(".optionType").attr("value","2");
     } else if($(this).val() == 3) {
        $(".answer2").css("display", "block");
         $(".answer3").css("display", "none");
         $(".answer4").css("display", "none");
         $(".answer1").css("display", "none");
         $(".options").css("display", "none");
         $(".optionType").attr("value","3");
     } else {
         $(".answer1").css("display", "block");
         $(".options").css("display", "none");
         $(".answer2").css("display", "none");
         $(".answer4").css("display", "none");
         $(".answer3").css("display", "none");
         $(".optionType").attr("value","4");
     }
     
   });
  });
JS;
$this->registerJs($js);
?>

<?php
namespace common\widgets\distpicker;

use yii\bootstrap\InputWidget;
use yii\helpers\Html;

/**
 * Created by PhpStorm.
 * Date: 2018/12/12
 * Time: 下午2:04
 */
class DistPicker extends InputWidget
{

    public $attributes = [
        'province',
        'city',
        'district'
    ];



    /**
     * @inheritdoc
     */
    public function run()
    {
        DistPickerAsset::register($this->getView());
       if(!$this->model->isNewRecord){
           $this->model->{$this->attribute} = "xx";
       }
        $province = Html::activeDropDownList($this->model, $this->attributes[0], [], [
            "class"         => "form-control",
            "data-province" => $this->model->{$this->attributes[0]} ?? ""
        ]);

        $city = Html::activeDropDownList($this->model, $this->attributes[1], [], [
            "class"         => "form-control",
            "data-city" => $this->model->{$this->attributes[1]} ?? ""
        ]);

        $district = Html::activeDropDownList($this->model, $this->attributes[2], [], [
            "class"         => "form-control",
            "data-district" => $this->model->{$this->attributes[2]} ?? ""
        ]);

        $id      = Html::getInputId($this->model, $this->attributes[2]);
        $input   = Html::activeHiddenInput($this->model, $this->attribute);
        $inputId = Html::getInputId($this->model, $this->attribute);
        $html    = "<div data-toggle=\"distpicker\" class='form-inline'>$province $city $district$input</div>";
        $js      = <<<JS
    $(document).on("change","#$id",function() {
        if ($(this).val()){
            $("#$inputId").val("xxx")
        }else{
            $("#$inputId").val("")
        }
    });
JS;
        $this->getView()->registerJs($js);

        echo $html;
    }

}
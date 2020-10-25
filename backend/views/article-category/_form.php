<?php

use common\models\ArticleCategory;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ArticleCategory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="article-category-form">

    <?php $form = ActiveForm::begin([
        'options' => ['class' => 'form-horizontal'],
        'fieldConfig' => [
            'options' => ['class' => 'form-group'],
            'template' => "{label}\n<div class=\"col-sm-8\">{input}\n<span class=\"help-block m-b-none\">{error}</span></div>",
            'labelOptions' => ['class' => 'col-sm-2 control-label'],
        ]
    ]); ?>

    <?php if ($model->isNewRecord): ?>
        <?= $form->field($model, 'p_id')->dropDownList(
            \yii\helpers\ArrayHelper::map(
                \common\models\ArticleCategory::makeCategory(ArticleCategory::find()
                    ->where([
                        'status' => 10,
                        'type' => $type,
                    ])->all()
                , 'form'),'id','name'),['prompt'=>'顶级分类']
        ) ?>
    <?php else:?>
        <?php if ($model->p_id == 0): ?>
            <?= $form->field($model, 'p_id')->dropDownList([0=>'顶级分类']) ?>
        <?php elseif($model->p->p_id == 0):?>
            <?= $form->field($model, 'p_id')->dropDownList(
                \yii\helpers\ArrayHelper::map(ArticleCategory::find()
                        ->where([
                            'status' => 10,
                            'type' => $type,
                            'p_id' => 0,
                        ])->all(),'id','name')
            ) ?>
        <?php else:?>
            <?= $form->field($model, 'p_id')->dropDownList(
                \yii\helpers\ArrayHelper::map(
                    \common\models\ArticleCategory::makeCategory(ArticleCategory::find()
                        ->where([
                            'status' => 10,
                            'type' => $type,
                        ])->all()
                        , 'form'),'id','name'),['prompt'=>'顶级分类']
            ) ?>
        <?php endif;?>
    <?php endif;?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => '请输入分类名称']) ?>

    <div class="form-group">
        <div class="col-sm-4 col-sm-offset-2">
            <?= Html::a('返回', $type == \common\models\Article::TYPE_TYKX ? ['index'] : ['list'], ['class' => 'btn btn-info']) ?>
            <?= Html::submitButton($model->isNewRecord ? '保存' : '保存', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

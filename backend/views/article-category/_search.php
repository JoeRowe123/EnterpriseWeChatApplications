<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ArticleCategorySearch */
/* @var $form yii\widgets\ActiveForm */
$model = new \common\models\ArticleCategory();
?>

<div class="article-category-search search-wrap">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => ['class' => 'form-inline'],
        'fieldConfig' => [
            'options' => ['class' => 'form-group'],
            'template' => "{input}"
        ],
    ]); ?>

    <?= $form->field($model, 'name') ->textInput(['placeholder'=>'请输入分类名', 'id' => 'name', 'value' => Yii::$app->request->get('name') ?? ''])?>

    <div class="form-group" style="margin-top:6px;">
        <?= Html::button('搜索', ['class' => 'btn btn-primary search']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<hr />

<?php
$action = $type == \common\models\Article::TYPE_WXT ? 'list' : 'index';
$js = <<<JS
    $(".search").on("click", function() {
      window.location.href = "$action?name="+$("#name").val();
    })
JS;
$this->registerJs($js);
?>

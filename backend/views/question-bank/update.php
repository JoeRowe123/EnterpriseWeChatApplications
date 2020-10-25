<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\QuestionBank */

$this->title = '编辑题库';
$this->params['breadcrumbs'][] = ['label' => '返回', 'url' => ['index']];
$this->params['breadcrumbs'][] = '编辑题库';
?>

<div class="article-update col-sm-12">
    <div class=" ibox ibox-content" style="margin-top: 15px;">
        <?= $this->render('_form', [
            'model' => $model
        ]) ?>
    </div>
</div>

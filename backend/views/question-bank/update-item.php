<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\QuestionBank */

$this->title = '编辑题目';
$this->params['breadcrumbs'][] = ['label' => '返回', 'url' => ['view', 'id' => $id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="article-create col-sm-12">
    <div class=" ibox ibox-content" style="margin-top: 15px;">
        <?= $this->render('add', [
            'model' => $model,
            'modelAttr' => $modelAttr,
            'id' => $id,
        ]) ?>
    </div>
</div>

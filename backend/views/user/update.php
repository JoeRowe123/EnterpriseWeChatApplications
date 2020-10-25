<?php

/* @var $this yii\web\View */
/* @var $model common\models\Article */
$this->title = '编辑管理员';
$this->params['breadcrumbs'][] = ['label' => '返回', 'url' => ['index']];
$this->params['breadcrumbs'][] = '编辑管理员';
?>

<div class="article-update col-sm-12">
	<div class=" ibox ibox-content" style="margin-top: 15px;">
    <?= $this->render('_form', [
        'model' => $model,
        'id' => $id,
    ]) ?>
	</div>
</div>
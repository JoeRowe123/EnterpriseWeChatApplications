<?php

/* @var $this yii\web\View */
/* @var $model common\models\Article */
$this->title = '编辑酷讯';
$this->params['breadcrumbs'][] = ['label' => '返回', 'url' => ['index']];
$this->params['breadcrumbs'][] = '编辑酷讯';
?>

<div class="article-update col-sm-12">
    <div class=" ibox ibox-content" style="margin-top: 15px;">
        <?= $this->render('_form', [
            'model' => $model,
            'type' => $type,
            'id' => false,
        ]) ?>
    </div>
</div>
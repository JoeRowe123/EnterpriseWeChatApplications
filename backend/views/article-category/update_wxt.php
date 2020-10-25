<?php

/* @var $this yii\web\View */
/* @var $model common\models\ArticleCategory */
$this->title = '修改分类';
$this->params['breadcrumbs'][] = ['label' => '返回', 'url' => ['list']];
$this->params['breadcrumbs'][] = '更新分类';
?>

<div class="article-category-update col-sm-12">
    <div class=" ibox ibox-content" style="margin-top: 15px;">
        <?= $this->render('_form', [
            'model' => $model,
            'type' => $type
        ]) ?>
    </div>
</div>
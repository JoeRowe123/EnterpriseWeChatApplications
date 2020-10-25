<?php

/* @var $this yii\web\View */
/* @var $model common\models\ArticleCategory */

$this->title = '创建分类';
$this->params['breadcrumbs'][] = ['label' => '返回', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="article-category-create col-sm-12">
    <div class=" ibox ibox-content" style="margin-top: 15px;">
        <?= $this->render('_form', [
            'model' => $model,
            'type' => $type
        ]) ?>
    </div>
</div>

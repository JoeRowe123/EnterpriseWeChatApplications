<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Article */

$this->title = '新建酷讯';
$this->params['breadcrumbs'][] = ['label' => '返回', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="article-create col-sm-12">
    <div class=" ibox ibox-content" style="margin-top: 15px;">
        <?= $this->render('_form', [
            'model' => $model,
            'type' => $type,
            'id' => $id,
        ]) ?>
    </div>
</div>

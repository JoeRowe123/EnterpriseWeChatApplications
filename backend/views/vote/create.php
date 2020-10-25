<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Vote */

$this->title = '新建投票调研';
$this->params['breadcrumbs'][] = ['label' => '投票管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vote-create col-sm-12">
    <div class=" ibox ibox-content" style="margin-top: 15px;">
        <?= $this->render('_form', [
            'model' => $model,
            'modelAttr' => $modelAttr
        ]) ?>
    </div>
</div>

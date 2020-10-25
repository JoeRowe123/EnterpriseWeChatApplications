<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Activity */

$this->title = '编辑活动';
$this->params['breadcrumbs'][] = ['label' => '活动助手', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="activity-create col-sm-12">
    <div class=" ibox ibox-content" style="margin-top: 15px;">
        <?= $this->render('_form', [
            'model' => $model,
            'modelAttr' => $modelAttr,
            'modelOptionAttr' => $modelOptionAttr,
        ]) ?>
    </div>
</div>


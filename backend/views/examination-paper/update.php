<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ExaminationPaper */

$this->title = 'Update Examination Paper: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => 'Examination Papers', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="examination-paper-update col-sm-12">
    <div class=" ibox ibox-content" style="margin-top: 15px;">
        <?= $this->render('_form', [
            'model' => $model
        ]) ?>
    </div>
</div>

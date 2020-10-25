<?php

/* @var $this \yii\web\View */

/* @var $content string */

use backend\assets\AdminAsset;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
AdminAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <style>
        .breadcrumbs {
            height: 40px;
            line-height: 40px;

        }

        .breadcrumbs ul {
            border-radius: 0;
            padding-left: 12px;
        }

        body {
            font-family: "微软雅黑", "Microsoft Yahei"
        }

        .table, th {
            text-align: center;
        }

        td input {
            text-align: center;
        }
    </style>
</head>
<body class="fixed-sidebar full-height-layout gray-bg">
<?php $this->beginBody() ?>
<div class="breadcrumbs">
    <?= Breadcrumbs::widget([
        'homeLink' => false,
        'links'    => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
    ]) ?>
</div>
<div class="print-log-create col-sm-12" style="margin-bottom: -15px;">
    <h1> </h1>
    <?= \common\widgets\Alert::widget() ?>
</div>
<?= $content ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

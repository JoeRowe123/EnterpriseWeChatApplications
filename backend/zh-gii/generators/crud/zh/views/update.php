<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();

echo "<?php\n";
?>

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */
$this->title = <?= $generator->generateString('修改' . $generator->getTableComments()) ?>;
$this->params['breadcrumbs'][] = ['label' => '返回', 'url' => ['index']];
$this->params['breadcrumbs'][] = <?= $generator->generateString('更新'. $generator->getTableComments()) ?>;
?>

<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-update col-sm-12">
	<div class=" ibox ibox-content" style="margin-top: 15px;">
    <?= "<?= " ?>$this->render('_form', [
        'model' => $model,
    ]) ?>
	</div>
</div>
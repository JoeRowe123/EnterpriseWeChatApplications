<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Vote */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => '投票管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style>

</style>

<div class="vote-view  wrapper row" style="display: flex">
            <div class="ibox-content col-sm-12" style="overflow: auto;max-height: 780px;">
            <table style="text-align:left" class="table table-bordered">
                <thead>
                <tr>
                    <th style="text-align:center;" colspan="4">
                        <h1 style="display:inline-block;"><?=$model->title?></h1>
                        <div style="float: right;margin-top: 6px"> <?= Html::a('导出Excel', ['export', 'id' => $model->id], ['class' => 'btn btn-success']) ?></div>
                    </th>
                </tr>
                </thead>
                <tbody>
                <td colspan="4">发起人：<?=$model->author_name?> <?=$model->vote_type == 1 ? '实名投票' : '匿名投票'?> 投票时间：<?=date("Y-m-d H:i:s", $model->start_time).' - '. date("Y-m-d H:i:s", $model->end_time)?> 参与人数: <?=$count?>
                </td>
                <?php foreach ($model->voteOptions as $k => $option):?>
                <tr>
                    <td><?=$k+1?></td>
                    <td><img src="<?=$option['option_image'][0] ?? ""?>" style="height:120px"/></td>
                    <td><?=$option['option_name']?></td>
                    <td><?=$option['num']?></td>
                </tr>
                <?php endforeach;?>
                </tbody>
            </table>
            </div>
<hr/>
            <div class="ibox-content col-sm-6" style="overflow: auto;max-height: 780px;">
            <table style="text-align:left" class="table table-bordered">
                <thead>
                <tr>
                    <th style="text-align:center" colspan="1">
                        <h1>投票记录</h1>
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($model->voteRecordsList as $k => $record):?>
                    <tr>
                        <?php if($model->vote_type == 2 ):?>
                            <td>"<?=date('Y-m-d H:i:s', $record['created_at'])?>"投票给: <?=implode(";",$record['desc'])?></td>
                        <?php else:?>
                            <td>"<?=date('Y-m-d H:i:s', $record['created_at'])?>" <?=$record['username']?> 投票给: <?=implode(";",$record['desc'])?></td>
                        <?php endif;?>
                    </tr>
                <?php endforeach;?>
                </tbody>
            </table>
    </div>
</div>

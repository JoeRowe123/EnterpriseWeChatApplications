<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Activity */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => '活动助手', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<ul class="nav nav-tabs" role="tablist" id="nav">
    <li  class="active">
        <a href="<?=\yii\helpers\Url::toRoute(['view', 'id' => $id])?>">活动详情</a>
    </li>
    <li>
        <a href="<?=\yii\helpers\Url::toRoute(['statistics', 'id' => $id])?>">报名统计</a>
    </li>
</ul>
<div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="base">
        <div class="article-index col-sm-12" style="margin-top: 15px;">
            <div class="ibox ibox-content">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'author_name',
                        'title',
                        'address',
                        [
                            "label" => '活动时间',
                            'value' => function ($m) {
                                return date("Y-m-d H:i:s", $m->start_time). ' - ' . date("Y-m-d H:i:s", $m->end_time);
                            }
                        ],
                        [
                            'attribute' => 'close_date',
                            'value' => function ($m) {
                                return date('Y-m-d H:i:s', $m->close_date);
                            }
                        ],
                        [
                            "attribute" => "content",
                            'format' => 'html',
                            "value" => function($model) {
                                return $model->content;
                            }
                        ],
                        "attachment",
                         [
                            "label" => "报名表单",
                            "format" => "raw",
                            "value" => function($model) {
                                $str = "";
                                if($model->items) {
                                    $str .= "<ul>";
                                    foreach ($model->items as $k => $item) {
                                        if($item->item_type == 1) {
                                            $type = "单选题";
                                        } elseif ($item->item_type == 2) {
                                            $type = "多选题";
                                        } else {
                                            $type = "问答题";
                                        }

                                        if ($item->is_must == 1) {
                                            $must = "必答";
                                        } else {
                                            $must = "选答";
                                        }
                                        $num = $k+1;
                                        $str .= "<li style='list-style-type:none; '>{$num}、$item->item_title($type/$must)</li>";
                                    }
                                    $str .= "</ul>";
                                }
                                return $str;
                            }
                        ],
                        "apply_num",
                        [
                            "attribute" => "is_push_msg",
                            "value" => function($model) {
                                return $model->is_push_msg == 1 ? "推送消息" : "不推送消息仅发布";
                            }
                        ]
                    ],
                ]) ?>
                    <?= Html::a('返回', ['index'], ['class' => 'btn btn-info']) ?>
            </div>
        </div>
    </div>

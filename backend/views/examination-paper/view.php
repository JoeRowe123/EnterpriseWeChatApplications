<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ExaminationPaper */

$this->title = '查看统计';
$this->params['breadcrumbs'][] = ['label' => '试卷列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="examination-paper-create col-sm-12">
        <p>试卷名称：<?=$model->name?></p>
        <p>考试时间：<?=date("Y-m-d H:i:s", $model->start_time). ' - ' . date("Y-m-d H:i:s", $model->end_time)?></p>
        <p>考试时长：<?=$model->duration_time?>分钟</p>
        <p>考试总分：<?=$model->total_grade?>分</p>
        <p>及格分数：<?=$model->pass_mark?>分</p>
        <p>考试范围：<?=implode(",",array_column($model->users, 'username'))?></p>
        <ul class="nav nav-tabs" role="tablist" id="nav">
            <li  class="<?=$status == 1 ? 'active' : ''?>">
                <a href="<?=\yii\helpers\Url::toRoute(['view', 'id' => $id, 'status' => 1])?>">已参与(<?=$model->participant_num?>)</a>
            </li>
            <li class="<?=$status == 0 ? 'active' : ''?>">
                <a href="<?=\yii\helpers\Url::toRoute(['view', 'id' => $id, 'status' => 0])?>">未参与(<?=$model->not_participant_num?>)</a>
            </li>
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="base">
                <div class="article-index col-sm-12" style="margin-top: 15px;">
                    <?php echo $this->render('join_search', ['model' => $searchModel, 'id' => $id]); ?>

                    <div class="ibox ibox-content">
                        <br>
                        <?= GridView::widget([
                            'dataProvider' => $dataProvider,
                            'tableOptions' => ['class' => 'table table-striped'],
                            'options' => ['class' => 'grid-view', 'style' => 'overflow:auto', 'id' => 'grid'],
                            'columns' => [
                                "username",
                                [
                                    'attribute' => 'department_name',
                                    'value' => function ($m) {
                                        return implode(",", array_column($m->department_name, 'name'));
                                    }
                                ],
                                [
                                    'label' => '开始时间',
                                    'value' => function ($m) {
                                        return date("Y-m-d H:i:s", $m->start_at);
                                    }
                                ],
                                [
                                    'label' => '结束时间',
                                    'value' => function ($m) {
                                        return date("Y-m-d H:i:s", $m->end_at);
                                    }
                                ],
                                "total_time",
                                "grade",
                                [
                                    'attribute' => 'status',
                                    'value' => function ($m) {
                                        return \common\models\ExaminationUsers::$status[$m->status];
                                    }
                                ],
                                [
                                    'class' => 'yii\grid\ActionColumn',
                                    'header' => '操作',
                                    'headerOptions' => ['width' => '150px'],
                                    'template' => '{view-paper} {go-over} ',
                                    'buttons' => [
                                        'go-over' => function ($url, $model, $key) {
                                            if($model->status == 2) {
                                                return Html::a('阅卷', $url, ['class' => 'btn btn-outline btn-danger btn-xs', 'style' => 'margin-top:5px;']);
                                            }
                                        },
                                        'view-paper' => function ($url, $model, $key) {
                                            return Html::a('查看答卷', $url, ['class' => 'btn btn-outline btn-info btn-xs', 'style' => 'margin-top:5px;']);
                                        }
                                    ],
                                ],
                            ],
                        ]); ?>
                    </div>
                </div>
            </div>

</div>

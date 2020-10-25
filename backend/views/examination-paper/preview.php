<?php

use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model common\models\ExaminationPaper */

$this->title = '预览试卷';
$this->params['breadcrumbs'][] = ['label' => '试卷列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile(Url::to('@web/js/paper/jquery.min.js'), ['position' => $this::POS_HEAD]);
$this->registerJsFile(Url::to('@web/js/paper/less.min.js'), ['position' => $this::POS_END]);
?>
<head>
    <link rel="stylesheet/less" href="<?=Url::to('@web/css/paper/style.less')?>">
</head>
<div class="examination-paper-create col-sm-12">
    <div class=" ibox ibox-content" style="margin-top: 15px;">
        <body>
        <div class="Preview_box">
            <h1 class="title">
                预览试卷
            </h1>
            <div class="content">
            </div>
            <div class="question">

            </div>
            <button type="button" class="btn btn-default" style="width:200px;" onclick="Back()">返回</button>

        </div>
        </body>
    </div>
</div>
<script>
    let allInfo = JSON.parse(sessionStorage.getItem('allInfo'));
    console.log(allInfo);
    let str = `<h1>${allInfo.name}</h1>
            <h2>${allInfo.startTime}-${allInfo.endTime}</h2>
            <h2>考试时长：${allInfo.duration}分钟  总分：${allInfo.totalScore}分 及格分：${allInfo.passingScore}分</h2>
            <h3>${allInfo.explain}</h3>
            `
    $('.Preview_box .content').html(str);

    let ti = '';
    let type = ['单选题','多选题','判断题','问答题'];
    let Letter = ['A','B','C','D','E','F','G','H','I','J','k'];

    allInfo.selList.forEach(el=>{
        let d = '';
        if(el.type == 1 || el.type == 2){
            let option = typeof(el.options)==='string'?JSON.parse(el.options):el.options;
            for (const i in option) {
                d += `<p>${i}:${option[i]}</p>`;
            }
        }
        //    let answer = typeof(el.answer)==='string'?JSON.parse(el.answer):el.answer;
        let  answer = el.answer;
        if(el.type == 2){
            answer = JSON.parse(answer).join('');
        }
        ti += `<div class='item'>
            <h1>(${type[el.type-1]}) ${el.title} (${el.grade}分) <span>答案：${answer}</span></h1>
            ${d}
        </div>`;
        return el;
    })
    $('.question').html(ti);

    function Back(){
        window.history.back(-1);
    }
</script>

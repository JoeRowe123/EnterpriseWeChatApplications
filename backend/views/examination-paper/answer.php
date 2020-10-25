<?php

use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model common\models\ExaminationPaper */

$this->title = '查看答卷';
$this->params['breadcrumbs'][] = ['label' => '返回', 'url' => ['view', 'id' => $model['paper_id']]];
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
                答卷详情
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
<?php $selList = json_encode($model['paperItems'])?>
<script>
    let str = `<h1><?=$model['paperInfo']['name']?></h1>
            <h2><?=date("Y-m-d H:i:s", $model['paperInfo']['start_time'])?>-<?=date("Y-m-d H:i:s", $model['paperInfo']['end_time'])?></h2>
            <h2>考试时长：<?=$model['paperInfo']['duration_time']?>分钟  总分：<?=$model['paperInfo']['total_grade']?>分 及格分：<?=$model['paperInfo']['pass_mark']?>分</h2>
            <h2 style="text-align:center"><?=$model['paperInfo']['explain']?></h3>
            `
    $('.Preview_box .content').html(str);

    let ti = '';
    let type = ['单选题','多选题','判断题','问答题'];
    let selList = <?=$selList?>;
    console.log(selList);
    selList.forEach(el=>{
        let d = '';
        if(el.item_type == 1 || el.item_type == 2){
            let option = typeof(el.item_option)==='string'?JSON.parse(el.item_option):el.item_option;
            for (const i in option) {
                d += `<p>${i}:${option[i]}</p>`;
            }
        }
        //    let answer = typeof(el.answer)==='string'?JSON.parse(el.answer):el.answer;
        let  answer = el.item_answer;
        let  y_answer = el.paperAnswer.answer;

        if(el.item_type == 2){
            answer = JSON.parse(answer).join('');
            y_answer = JSON.parse(y_answer).join('');
        } else {
            //替换所有的换行符
            y_answer = JSON.parse(y_answer).replace(/[\n]/g,'<br>')
        }


        let  resultTrue = "<span style='color: blue;margin-left: 10px;'><i class='fa fa-check'></i>回答正确</span>";
        let  resultFalse= "<span style='color: red;margin-left: 10px;'><i class='fa fa-close'></i>回答错误</span>";
        let  resultWait= "<span style='color: skyblue;margin-left: 10px;'><i class='fa fa-clock-o'></i>待阅卷</span>";
        let res = "";
        if(el.paperAnswer.is_true === "1") {
            res = resultTrue
        } else if(el.paperAnswer.is_true === "-1") {
            res = resultWait
        } else {
            res = resultFalse
        }
        ti += `<div class='item'>
            <h1>${el.id}、(${type[el.item_type-1]}) ${el.item_title} (${el.item_grade}分) ${res}</h1>
            ${d}
             <h1> <span style="color: black">您的答案：${y_answer}</span></h1>
             <h1> <span>答案：${answer}</span></h1>
        </div>`;
        return el;
    })
    $('.question').html(ti);

    function Back(){
        window.history.back(-1);
    }
</script>

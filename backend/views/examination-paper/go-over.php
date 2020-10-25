<?php

use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model common\models\ExaminationPaper */

$this->title = '阅卷';
$this->params['breadcrumbs'][] = ['label' => '返回', 'url' => ['view', 'id' => $model['paper_id']]];
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile(Url::to('@web/js/paper/jquery.min.js'), ['position' => $this::POS_HEAD]);
$this->registerJsFile(Url::to('@web/js/paper/less.min.js'), ['position' => $this::POS_END]);
?>
<head>
    <link rel="stylesheet/less" href="<?=Url::to('@web/css/paper/style.less')?>">
</head>
<div class="vote-view  wrapper row" style="display: flex">
<div class="examination-paper-create col-sm-12" style="overflow: auto;max-height: 780px;">
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
<div class="ibox-content col-sm-4" style="overflow: auto;max-height: 780px;">
    <div class="" style="margin-top: 15px;">
        <body>
        <div class="Preview_box">
            <h1 class="title">
                阅卷
            </h1>
            <h1 class="title">
                题目编号
            </h1>
            <h1 class="title">
                温馨提示：已阅题目不可修改
            </h1>
            <div class="question-btn" style="margin-bottom: 20px;width: 350px; word-wrap: break-word;
            word-break: break-all;
            overflow: auto;">

            </div>
            <div style="margin-bottom: 20px;">得分（若答案可酌情给分，可直接输入分数）</div>
            <div style="margin-bottom: 20px;">
                <span style="margin-right: 15px;">
                    <input type="radio" name="is_true" value="0" checked onclick="InserGrade(0)">错误
                </span>
                <input type="radio" name="is_true" value="1" onclick="InserGrade(1)">正确
            </div>
            <div style="margin-bottom: 20px;"><input type="number" name="grade" min="0" placeholder="请输入分数"/>分</div>

            <h1 style="margin-bottom: 20px;">得分 <?=$model['grade']?>分</h1>

            <div style="display: inline-flex">
                <button type="button" class="btn btn-default" style="margin-right: 15px;" onclick="Back()">返回</button>
                <button type="button" class="btn btn-success" style="" onclick="Submit()">提交</button>
            </div>

        </div>
        </body>
    </div>
</div>
</div>
<?php $selList = json_encode($model['paperItems'])?>
<?php $waitList = json_encode($wait)?>
<?php $url = Url::to('paper-inspection')?>
<?php $backUrl = Url::to('view')?>
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
        ti += `<div class='item' id="item-${el.id}">
            <h1>${el.id}、(${type[el.item_type-1]}) ${el.item_title} (${el.item_grade}分) ${res}</h1>
            ${d}
             <h1> <span style="color: black">您的答案：${y_answer}</span></h1>
             <h1> <span>答案：${answer}</span></h1>
        </div>`;
        return el;
    })
    $('.question').html(ti);

    let btnStr = "";
    let waitList = <?=$waitList?>;
    waitList.forEach(el=>{
        btnStr += `
<a href="#item-${el.paperItem.id}" type="button" class="btn btn-default" style="margin-top:5px;margin-right: 15px;" data-id="${el.id}" data-grade="${el.paperItem.item_grade}" onclick="return Choose(this)">${el.paperItem.id}</a>
`;
        return el;
    });
    $('.question-btn').html(btnStr);

    function Choose(t) {
        if($(t).hasClass("btn-success")) {
            $(t).removeClass("btn-success")
            $(t).addClass("btn-default")
        } else {
            $(t).addClass("btn-success")
            $(t).removeClass("btn-default")
            $(t).siblings().removeClass("btn-success");
            $(t).siblings().addClass("btn-default");
        }
        window.location.href = t.href;
        return false;
    }
    let SubData = {};
    function Submit() {
        SubData.is_true = $("input[name='is_true']").filter(':checked').val();
        SubData.grade = $("input[name='grade']").val();
        SubData.id = 0;
        $(".question-btn .btn").each(function(i,o){
            if($(o).hasClass("btn-success"))
                SubData.id = $(o).data("id");
        });
        if(SubData.id === 0) {
            alert("请选择要阅的题目");
            return false;
        }
        if(!SubData.grade) {
            alert("请输入分数");
            return false;
        }

        if(SubData.grade < 0) {
            alert("分数不能为负数");
            return false;
        }
        var totalGrade = 0;
        $(".question-btn .btn").each(function(i,o){
            if($(o).hasClass("btn-success")) {
                totalGrade = $(o).data("grade");
                return false;
            }
        });
        if(SubData.grade > totalGrade) {
            alert("分数不能大于该题的总分");
            return false;
        }

        $.ajax({
            type: "POST",
            url: "<?=$url?>",
            data: JSON.stringify(SubData),
            dataType: "json",
            success: function(data){
                if(data.status == 1) {
                    layer.msg(data.msg, {time:1000}, function () {
                        window.location.reload();
                    })
                } else {
                    layer.msg(data.msg, {time:1500})
                }
            }
        });
    }

    function InserGrade(type) {
        if(type === 1) {
            $(".question-btn .btn").each(function(i,o){
                if($(o).hasClass("btn-success")) {
                    $("input[name='grade']").val($(o).data("grade"));
                    return false;
                }
            });
        } else {
            $("input[name='grade']").val(0);
        }

    }
    
    function Back(){
        window.location.href = "<?=$backUrl?>?id=<?=$model['paper_id']?>&status=1";
    }
</script>

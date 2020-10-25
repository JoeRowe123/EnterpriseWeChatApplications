<?php

use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model common\models\ExaminationPaper */

$this->title = $id ? '编辑试卷' : '新建试卷';
$this->params['breadcrumbs'][] = ['label' => '试卷列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile(Url::to('@web/js/paper/jquery.min.js'), ['position' => $this::POS_HEAD]);
$this->registerJsFile(Url::to('@web/js/paper/less.min.js'), ['position' => $this::POS_END]);
$step2 = Url::to("step2");
$step3 = Url::to("step3");
?>
<head>
    <link rel="stylesheet/less" href="<?=Url::to('@web/css/paper/style.less')?>">
</head>
<div class="examination-paper-create col-sm-12">
        <div class=" ibox ibox-content" style="margin-top: 15px;">
            <body>
            <div class="Step_box">
                <div class="item active">
            <span>
                1
            </span>
                    <p>
                        模式设置
                    </p>
                </div>
                <div class='line'></div>
                <div class="item">
            <span>
                2
            </span>
                    <p>
                        模式设置
                    </p>
                </div>
                <div class='line'></div>
                <div class="item">
            <span>
                3
            </span>
                    <p>
                        模式设置
                    </p>
                </div>
            </div>
            <div class="Form_box">
                <form class="form-horizontal">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-1 control-label">试卷名称</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="Name" placeholder="最多输入64个字符" maxlength="64">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-1 control-label">选题模式</label>
                        <div class="col-sm-10">
                            <select class="form-control" id="Type" onchange="TypeChange()">
                                <option value="1" selected>自主选题</option>
                                <option value="2">固定规则选题</option>
                                <option value="3">随机生成题目</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-1 control-label">选择题库</label>
                        <div class="col-sm-10 checkbox">
                            <div id="QuestionBank">
                            </div>
                            <br>
                            <label class="checkbox-inline"  id="All">
                                <input type="checkbox" value="all"> 使用所选题库的所有题目
                            </label>
                        </div>
                    </div>
                    <div class="form-group" id="Choice_q">
                        <label for="inputEmail3" class="col-sm-1 control-label">抽题规则设置</label>
                        <div class="col-sm-10">
                            <div class="Form_item_box">
                        <span>
                            单选题总数： <em>0</em>  选择
                        </span>
                                <input type="number" class="form-control num" id="One" min="0" oninput="value=value.replace(/[^\d]/g,'')">
                                <span>题，每题</span>
                                <input type="number" class="form-control score" min="0">
                                <span>分</span>
                            </div>
                            <div class="Form_item_box">
                        <span>
                            多选题总数：<em>0</em>  选择
                        </span>
                                <input type="number" class="form-control num" min="0" oninput="value=value.replace(/[^\d]/g,'')">
                                <span>题，每题</span>
                                <input type="number" class="form-control score" min="0">
                                <span>分</span>
                            </div>
                            <div class="Form_item_box">
                        <span>
                            判断题总数：<em>0</em>  选择
                        </span>
                                <input type="number" class="form-control num" min="0" oninput="value=value.replace(/[^\d]/g,'')">
                                <span>题，每题</span>
                                <input type="number" class="form-control score" min="0">
                                <span>分</span>
                            </div>
                            <div class="Form_item_box">
                        <span>
                            问答题总数：<em>0</em>  选择
                        </span>
                                <input type="number" class="form-control num" min="0" oninput="value=value.replace(/[^\d]/g,'')">
                                <span>题，每题</span>
                                <input type="number" class="form-control score" min="0">
                                <span>分</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-1 control-label"></label>
                        <div class="col-sm-10">
                            <button type="button" class="btn btn-success" style="width:200px;" onclick="Next()">下一步</button>
                        </div>
                    </div>
                </form>
            </div>
            </body>
        </div>
</div>

<script>
    sessionStorage.setItem('allInfo','');
    let baseIp = "<?=Yii::$app->params['backApi']?>";
    let allBank = [];
    let oldInfo = {};
    let SType = '';
    $(function(){
        let url = window.location.href.split('?')[1];
        SType = new URLSearchParams(url).get('type');

        if(SType == 'edit'){
            let id = new URLSearchParams(url).get('id');
            let url_ = baseIp + 'paper-detail?id=' + id;
            $.get(url_,function(data){
                console.log(data);
                oldInfo = data;
                $('#Name').val(oldInfo.name);
                $('#Type').val(oldInfo.type);
                if(oldInfo.is_all === '1'){
                    $('#All input')[0].checked = true;
                }
                if(oldInfo.type != 1){// 固定 规则
                    let rule = JSON.parse(oldInfo.questions_rule);
                    let num = $('#Choice_q .num');
                    Array.from(num).forEach((el,index)=>{
                        $('#Choice_q .num').eq(index).val(rule[index].val);
                        $('#Choice_q .score').eq(index).val(rule[index].score);
                    })
                }
                TypeChange();
                GetAllBank();

            })
        }else{
            GetAllBank();
            TypeChange();
            BaseSet();
        }
    })
    function GetAllBank (){
        let url = baseIp + 'all-question-bank';
        $.get(url,(data)=>{
            allBank = data;
            SetBank();
        },)
    }
    function numChange(){
        alert()
    }
    function BaseSet(){
        $('#Choice_q .num').on('blur',function(){
            let index = $('#Choice_q .num').index($(this));
            let max = $('#Choice_q em').eq(index).html();
            if($(this).val()<0){
                alert('选择题目数不能为负数');
                return false;
            }

            if(parseInt($(this).val())>max){
                alert('选择题目数不能大于该类型题目总数');
                return false;
            }
        })

        $('#Choice_q .score').on('blur',function(){
            if($(this).val()<0){
                alert('题目分数不能为负数');
                return false;
            }
        })
    }
    function SetBank(){
        let str = '';
        allBank.forEach(el=>{
            let check = '';
            if(SType == 'edit'){
                let c = JSON.parse(oldInfo.question_bank_ids);
                if(c.indexOf(el.id)>-1){
                    check = 'checked';
                }
            }
            str += `<label class="checkbox-inline">
                            <input type="checkbox" value="${el.id}" ${check}> ${el.name}
                        </label>`;
            return el;
        })
        $('#QuestionBank').html(str);
        $('#QuestionBank input').on('change',BankChange);
        BankChange();
    }
    function TypeChange(){
        let v = $('#Type').val();
        if(v == 2){
            $('#Choice_q').show();
            $('#All').hide();
        }else if(v==1){
            $('#All').show();
            $('#Choice_q').hide();
        }else{
            $('#Choice_q').show();
            $('#All').hide();
        }
    }
    function BankChange(){
        let bank = $('#QuestionBank input');
        let sel = [];
        Array.from(bank).forEach((el,index)=>{
            if(el.checked){
                sel.push(allBank[index]);
            }
            return el;
        })
        let type = ['single_num','multiple_num','judge_num','gap_filling_num'];
        let num = [0,0,0,0];
        sel.forEach(el=>{
            type.forEach((ele,index)=>{
                num[index] += parseInt(el[ele]);
                return ele;
            })
            return el;
        })
        let all = $('#Choice_q em');
        num.forEach((el,index)=>{
            all.eq(index).html(el);
            return el;
        })
    }

    function Next(){
        let info = {};
        info.name = $('#Name').val().replace(/(^\s*)|(\s*$)/g, "");
        info.type = $('#Type').val();
        let ids = $('#QuestionBank input');
        ids = Array.from(ids).filter(el=>{
            return el.checked;
        })
        ids = Array.from(ids).map(el=>{
            return el.value;
        })
        info.ids = ids;
        if(!!!info.name){
            alert('请输入试卷名称！');
            return false;
        }
        if(ids.length<=0){
            alert('请选择题库！');
            return false;
        }
        if(info.type==1){
            let all = $('#All input')[0].checked;
            info.all = all;
            if(SType == 'edit'){
                let pan = true;
                let selItem = JSON.parse(oldInfo.question_bank_ids);
                if(selItem.length === info.ids.length){
                    selItem.forEach(el=>{
                        if(!info.ids.includes(el)){
                            pan = false;
                        }
                    })
                }else{
                    pan = false;
                }
                let all_ = oldInfo.is_all === '1'?true:false;
                if(info.all != all_){
                    pan = false;
                }
                if(!pan){
                    oldInfo.items = [];
                }
                sessionStorage.setItem('oldInfo',JSON.stringify(oldInfo));
            }else{
                sessionStorage.setItem('oldInfo','');
            }
            sessionStorage.setItem('TestInfo',JSON.stringify(info));
            if(all){
                window.location.href = '<?=$step3?>?type=<?=$type?>&id=<?=$id?>';
            }else{
                window.location.href = '<?=$step2?>?type=<?=$type?>&id=<?=$id?>';
            }
        }
        if(info.type==2){
            let num = $('#Choice_q .num');
            let question = [];
            Array.from(num).forEach((el,index)=>{
                let max = $('#Choice_q em').eq(index).html();
                let score = $('#Choice_q .score').eq(index).val();
                score = score === ''?0:score;
                score = parseInt(score);

                if(parseInt(el.value)>max){
                    alert('选择题目数不能大于该类型题目总数');
                    return false;
                }
                let item = {
                    type:index,
                    val:el.value,
                    score:score,
                }
                question.push(item);
                return el;
            })
            let pan = false;
            question.forEach(el=>{
                if(el.val >0){
                    pan = true;
                }
                if(el.val > 0 && el.score <= 0){
                    pan = false;
                }
                return el;
            })
            if(!pan){
                alert('请完善抽题规则设置！');
                return false;
            }
            if(SType == 'edit'){
                let pan = true;
                let selItem = JSON.parse(oldInfo.question_bank_ids);
                if(selItem.length === info.ids.length){
                    selItem.forEach(el=>{
                        if(!info.ids.includes(el)){
                            pan = false;
                        }
                    })
                }else{
                    pan = false;
                }
                let oldRule = JSON.parse(oldInfo.questions_rule);
                if(oldRule.length === question.length){
                    oldRule.forEach(el=>{
                        let d = false;
                        question.forEach(ele=>{
                            if(ele.type == el.type && el.val === ele.val){
                                d = true;
                            }
                        })
                        if(!d){
                            pan = false;
                        }
                    })
                }else{
                    pan = false;
                }
                if(!pan){
                    oldInfo.items = [];
                }
                sessionStorage.setItem('oldInfo',JSON.stringify(oldInfo));
            }else{
                sessionStorage.setItem('oldInfo','');
            }
            info.rule = question;
            sessionStorage.setItem('TestInfo',JSON.stringify(info));
            window.location.href = '<?=$step2?>?type=<?=$type?>&id=<?=$id?>';
        }
        if(info.type==3){
            let num = $('#Choice_q .num');
            let question = [];
            Array.from(num).forEach((el,index)=>{
                let max = $('#Choice_q em').eq(index).html();
                let score = $('#Choice_q .score').eq(index).val();
                score = score === ''?0:score;
                score = parseInt(score);
                if(parseInt(el.value)>max){
                    alert('选择题目数不能大于该类型题目总数');
                    return false;
                }
                let item = {
                    type:index,
                    val:el.value,
                    score:score,
                }
                question.push(item);
                return el;
            })
            let pan = false;
            question.forEach(el=>{
                if(el.val >0){
                    pan = true;
                }
                if(el.val > 0 && el.score <= 0){
                    pan = false;
                }
                return el;
            })
            if(!pan){
                alert('请完善抽题规则设置！');
                return false;
            }
            if(SType == 'edit'){
                let pan = true;
                let selItem = JSON.parse(oldInfo.question_bank_ids);
                if(selItem.length === info.ids.length){
                    selItem.forEach(el=>{
                        if(!info.ids.includes(el)){
                            pan = false;
                        }
                    })
                }else{
                    pan = false;
                }
                let oldRule = JSON.parse(oldInfo.questions_rule);
                if(oldRule.length === question.length){
                    oldRule.forEach(el=>{
                        let d = false;
                        question.forEach(ele=>{
                            if(ele.type == el.type && el.val === ele.val){
                                d = true;
                            }
                        })
                        if(!d){
                            pan = false;
                        }
                    })
                }else{
                    pan = false;
                }
                if(!pan){
                    oldInfo.items = [];
                }
                sessionStorage.setItem('oldInfo',JSON.stringify(oldInfo));
            }else{
                sessionStorage.setItem('oldInfo','');
            }
            info.rule = question;
            sessionStorage.setItem('TestInfo',JSON.stringify(info));
            window.location.href = '<?=$step3?>?type=<?=$type?>&id=<?=$id?>';
        }
    }
</script>

<?php

use yii\helpers\Html;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model common\models\ExaminationPaper */

$this->title = '新建试卷';
$this->params['breadcrumbs'][] = ['label' => '试卷列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile(Url::to('@web/js/paper/jquery.min.js'), ['position' => $this::POS_HEAD]);
$this->registerJsFile(Url::to('@web/js/paper/less.min.js'), ['position' => $this::POS_END]);
$index = Url::to('create');
$step3 = Url::to("step3");
?>
<head>
    <link rel="stylesheet/less" href="<?=Url::to('@web/css/paper/style.less')?>">
</head>
<div class="examination-paper-create col-sm-12">
    <div class=" ibox ibox-content" style="margin-top: 15px;">
        <body>
        <div class="Step_box">
            <div class="item ">
            <span>
                1
            </span>
                <p>
                    模式设置
                </p>
            </div>
            <div class='line'></div>
            <div class="item active">
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
        <div style="margin:20px;">
            <table class="table table-bordered Table_box_" id="Table">
                <thead></thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <div class="pageList">
        </div>
        <div style="text-align: center;margin: 20px;">
            <button type="button" class="btn btn-success" style="width:200px;" onclick="Next(0)">上一步</button>
            <button type="button" class="btn btn-success" style="width:200px;" onclick="Next(1)">下一步</button>
            <button type="button" class="btn btn-success" id="ReGet" style="width:200px;display: none;" onclick="ReGet()">重新抽题</button>

        </div>
        </body>
    </div>
</div>
<script>
    let baseIp = "<?=Yii::$app->params['backApi']?>";
    let TestInfo = {};
    let SearchRes = {};
    let pageInfo = {
        total:0,
        size:5,
        page:1,
    };
    let selList=[],dataList=[];
    let Stype = '';
    (function(){
        TestInfo = JSON.parse(sessionStorage.getItem('TestInfo'));
        let thead = $('#Table thead');
        let head = ['题库名称','题目名称','题型','分数','排序'];
        let headStr = `<tr><th style='width:100px;'><input type="checkbox" class='all' ${TestInfo.type == 2 ? 'disabled' : ''}></th>`;
        head.forEach(el=>{
            headStr += `<th> ${el} </th>`;
        })
        headStr += `</tr>`;
        thead.html(headStr);
        $('#Table thead input').on('change',SelChange);

        let oldInfo = sessionStorage.getItem('oldInfo');
        if(!!oldInfo){
            Stype = 'edit';
            oldInfo = JSON.parse(oldInfo);
            SearchRes = oldInfo.items.map(el=>{
                let serle = {};
                serle.resIds = el.item_id;
                serle.grade = el.item_grade;
                return serle;
            });

            selList = oldInfo.items.map(el=>{
                let ele = {};
                ele.id = el.item_id;
                ele.title = el.item_title;
                ele.type = el.item_type;
                ele.bank_id = el.bank_id;
                ele.bank_name = el.bank_name;
                ele.sort = el.sort;
                ele.grade = el.item_grade;
                ele.answer = el.item_answer;
                if(!!el.item_option){
                    if(typeof(el.item_option) === 'string'){
                        el.item_option = JSON.parse(el.item_option);
                    }
                }else{
                    el.item_option = []
                }
                ele.options = el.item_option;
                return ele;
            });
        }

        if(TestInfo.type == 1){
            GetTable();
        }
        if(TestInfo.type == 2){
            GetSun('first');
            $('#ReGet').show();
        }
    })()
    function Next(type){
        if(type===0){
            window.location.href = '<?=$index?>?type=<?=$type?>&id=<?=$id?>';
        }else{

            if(selList.length<=0){
                alert('请选择问题！');
                return false;
            }
            let is_sort = false;
            selList.forEach(el=>{
                if(el.sort < 0) {
                    is_sort = true;
                    return false;
                }
            });

            if(is_sort) {
                alert("排序不能为负数");
                return false;
            }

            TestInfo.selList = selList;
            sessionStorage.setItem('TestInfo',JSON.stringify(TestInfo));
            window.location.href = '<?=$step3?>?type=<?=$type?>&id=<?=$id?>';
        }
    }
    function GetSun(type){
        let url = baseIp + `random-topic?page=${pageInfo.page}&pageSize=${pageInfo.size}`;
        let topicArr = {};
        TestInfo.rule.forEach((el,index)=>{
            let s = index + 1;
            topicArr[s] = {
                num:el.val,
                grade:el.score,
            }
        })
        let info = {
            ids:TestInfo.ids,
            topicArr:topicArr,
            searchRes:SearchRes
        };

        $.ajax({
            type:'post',
            url,
            data:JSON.stringify(info),
            success:function(data){
                if(pageInfo.total === 0) {
                    SetPage(data.total);
                }
                SearchRes = data.searchRes;
                pageInfo.total = data.total;

                if(type == 'first') {
                    selList = data.totalItems.map(el=>{
                        el.item.grade = el.grade;
                        for (let i =0; i < selList.length; i++) {
                            let ele = selList[i];
                            if(ele.id == el.item.id) {
                                el.item.sort = ele.sort;
                                break;
                            }
                        }
                        return el.item;
                    })
                }
                dataList = data.items.map(el=>{
                    el.item.grade = el.grade;
                    for (let i =0; i < selList.length; i++) {
                        let ele = selList[i];
                        if(ele.id == el.item.id) {
                            el.item.sort = ele.sort;
                            break;
                        }
                    }
                    return el.item;
                });
                TableSet(dataList);
            }
        })
    }
    function GetTable(){
        let url = baseIp + `all-topic?page=${pageInfo.page}&pageSize=${pageInfo.size}`;
        let info = {
            ids:TestInfo.ids,
        };
        $.ajax({
            type:'post',
            url,
            data:info,
            success:function(data){
                if(pageInfo.total===0){
                    SetPage(data.total);
                }
                pageInfo.total = data.total;
                dataList = data.items;
                TableSet(data.items);
            }
        })
    }

    function TableSet(list){
        let str = '';
        let type = ['单选题','多选题','判断题','问答题'];
        list.forEach(el=>{
            str += `
                <tr>
                    <td>
                        <input type="checkbox" class='check_' ${TestInfo.type == 2 ? 'disabled' : ''}></th>
                    </td>
                    <td>
                        ${el.questionBank.name}
                    </td>
                    <td>
                        ${el.title}
                    </td>
                    <td>
                        ${type[el.type-1]}
                    </td>
                    <td>
                        ${el.grade}
                    </td>
                    <td>
                        <input type="number" value='${el.sort}' name="" id="" class='Sort' placeholder="请输入排序值" min="0">
                    </td>
                </tr>
            `;
        })
        $('#Table thead input')[0].checked = false;
        $('#Table tbody').html(str);
        $('#Table tbody .check_').on('change',SelChange);
        $('#Table tbody .Sort').on('blur',SortChange);
        SelSet();
    }
    function ReGet(){
        pageInfo = {
            total:0,
            size:5,
            page:1
        }
        SearchRes = [];
        GetSun("first");
    }
    function SortChange(){
        let index = $('#Table tbody .Sort').index($(this));
        let val = $(this).val();
        dataList[index].sort = val;
        selList.forEach(el=>{
            if(el.id == dataList[index].id){
                el.sort = val;
            }
            return el;
        })

    }
    function SelSet(){
        if(selList.length>0){
            dataList.forEach((el,index)=>{
                let pan = false;
                selList.forEach(ele=>{
                    if(ele.id == el.id){
                        pan = true;
                    }
                    if(!!ele.item_id && ele.item_id == el.id){
                        pan = true;
                    }
                })
                if(pan){
                    $('#Table tbody .check_').eq(index)[0].checked = true;
                }
            })
            SelPan();
        }
    }
    function SelChange(){
        let class_ = $(this).attr('class');
        let check = $(this)[0].checked;
        if(class_ === 'all'){
            if(check){
                let o = selList.concat(dataList);
                o = Array.from(new Set(o));
                selList = o;
            }else{
                dataList.forEach(el=>{
                    let ind = '';
                    selList.forEach((ele,index)=>{
                        if(ele.id = el.id){
                            ind = index;
                        }
                    })
                    if(!!ind){
                        selList.splice(ind,1);
                    }
                })
            }
            Array.from($('#Table tbody .check_')).forEach(el=>{
                el.checked = check
            })
        }else{
            let index = $('#Table tbody .check_').index($(this));
            if(check){
                selList.push(dataList[index]);
            }else{
                let ind = selList.indexOf(dataList[index]);
                if(ind>-1){
                    selList.splice(ind,1);
                }
            }
            SelPan();
        }
    }
    function SelPan(){
        let pan = true;
        Array.from($('#Table tbody .check_')).forEach(el=>{
            if(!el.checked){
                pan = false;
            }
        })
        $('#Table thead input')[0].checked = pan;
    }
    function SetPage(total){
        let page = Math.ceil(total/pageInfo.size);
        let str = '';
        for (let i = 0; i < page; i++) {
            str += `<span>${i+1}</span>`;
        }
        $('.pageList').html(str);
        $('.pageList span').eq(0).addClass('active');
        $('.pageList span').on('click',function(){
            $('.pageList span').removeClass('active');
            $(this).addClass('active');
            pageInfo.page = $(this).index() + 1;
            if(TestInfo.type == 1) {
                GetTable();
            } else {
                GetSun();
            }

        })
    }
</script>

<?php

use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model common\models\ExaminationPaper */

$this->title = '新建试卷';
$this->params['breadcrumbs'][] = ['label' => '试卷列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile(Url::to('@web/js/paper/jquery.min.js'), ['position' => $this::POS_HEAD]);
$this->registerJsFile(Url::to('@web/js/paper/less.min.js'), ['position' => $this::POS_END]);
$preview = Url::to("preview");
$url = Url::to("save-data");
$listUrl = Url::to("index");
?>
<head>
    <link rel="stylesheet/less" href="<?=Url::to('@web/css/paper/style.less')?>">
    <link href="https://cdn.bootcss.com/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
    <script src="https://cdn.bootcss.com/moment.js/2.22.0/moment-with-locales.js"></script>
    <script src="https://cdn.bootcss.com/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
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
            <div class="item ">
            <span>
                2
            </span>
                <p>
                    模式设置
                </p>
            </div>
            <div class='line'></div>
            <div class="item active">
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
                    <label for="inputEmail3" class="col-sm-1 control-label">考试范围</label>
                    <div class="col-sm-10" id="Fanwei">
                        <button type="button" class="btn btn_1 btn-success">全公司</button>
                        <button type="button" class="btn btn_1">指定对象</button>
                        <label class="checkbox-inline" style="margin:0 10px;margin-top:-5px;">
                            <input type="checkbox" id="notice"> 发送考试通知题型
                        </label>
                        <span style="color:#666;line-height:34px;">
                        温馨提示：若允许，将给用户企业微信推送考试信息
                    </span>
                        <div class="Ren_">
                            <label>选择人员：</label>
                            <div class="Ren_sel">
                                <button type="button" class="btn btn-default" id="Sel_ren">选择人员</button>
                                <button type="button" class="btn btn-default" onclick="ClearRen()">清空人员</button>
                                <div class="all_sel">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-1 control-label">考试时间</label>
                    <div class="col-sm-10">
                        <div class="col-sm-3" style="padding:0;">
                            <div class='input-group date' id='datetimepicker1'>
                                <input type='text' class="form-control" placeholder="开始时间" />
                                <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                            </div>
                        </div>
                        <span style="line-height: 34px;margin:0 10px;float: left;">至</span>
                        <div class="col-sm-3"  style="padding:0;">
                            <div class='input-group date' id='datetimepicker2'>
                                <input type='text' class="form-control" placeholder="结束时间" />
                                <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-1 control-label">考试时长</label>
                    <div class="col-sm-3">
                        <input type="number" class="form-control" id="duration"  placeholder="输入正整数，单位是分钟" >
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-1 control-label">考试提醒</label>
                    <div class="col-sm-10">
                        <label class="checkbox-inline" style="margin:0 10px;margin-top:-7px;">
                            <input type="checkbox" id="advancePush"> 开启考试提醒功能
                        </label>
                        <span style="color:#666;line-height:30px;">
                        温馨提示：若允许，将在考试开始前30分钟推送消息
                    </span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-1 control-label">及格分数</label>
                    <div class="col-sm-3">
                        <input type="num" class="form-control" id="passingScore"  placeholder="及格分数" >
                    </div>
                    <span style="color:#666;line-height:30px;">
                    注： 当前考试总分为： <em id="Total">20</em> 分
                </span>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-1 control-label">考试说明</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" id="explain" rows="3" placeholder="最多输入500个字符" maxlength="500"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-1 control-label"></label>
                    <div class="col-sm-10">
                        <button type="button" class="btn btn-default" style="width:200px;" onclick="Back()">上一步</button>
                        <button type="button" class="btn btn-success" style="width:200px;" onclick="Next(0)">预览</button>
                        <button type="button" class="btn btn-success" style="width:200px;" onclick="Next(1)">保存草稿</button>
                        <button type="button" class="btn btn-success" style="width:200px;" onclick="Next(2)" >发布</button>

                    </div>
                </div>
            </form>
        </div>

        <div class="Sel_ren_">
            <div class="Sel_ren_box">
                <div class="title">
                    选择人员
                </div>
                <div class="Sel_search">
                    <div class="one">
                        <input type="text" placeholder="搜索部门" id="Bumen">
                        <button type="button" class="btn btn-default" onclick="SearchBu()">搜索</button>
                    </div>
                    <div class="two">

                    </div>
                    <div class="one">
                        <input type="text" placeholder="搜索人员" id="Ren">
                        <button type="button" class="btn btn-default" onclick="SearchRen()">搜索</button>
                    </div>
                </div>
                <div class="S_2_box">
                    <div class="S_2_box_1">

                    </div>
                    <div class="S_2_box_2">
                        <div class="S_2_box_2_1">
                            <button type="button" class="btn" onclick="All()">全选</button>
                            <button type="button" class="btn" onclick="Fan()">反选</button>
                            <button type="button" class="btn" onclick="Clear()">清空</button>
                        </div>
                        <div class="S_2_box_2_2">

                        </div>
                        <div class="S_2_box_2_1">
                            <button type="button" class="btn btn-default" onclick="Cancel()">取消</button>
                            <button type="button" class="btn btn-primary" onclick="Confirm()">确定</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </body>
    </div>
</div>
<script>
    let allInfo = JSON.parse(sessionStorage.getItem('TestInfo'));
    let baseIp = "<?=Yii::$app->params['backApi']?>";
    let SelRen = []; // 已选人员
    let SelRenId = [];// 已选人员 id
    let totalScore = 0;
    let allOrganization = [];
    let allUser = [];
    let baseBu = {};
    let currentUser = [];
    let YuanRen = [];
    let YuanBu = [];
    let NowBu = [];
    let Stype = '';
    let currentList = [];

    let all_ = sessionStorage.getItem('allInfo');
    if(!!all_){
        allInfo = JSON.parse(all_);
    }

    let oldInfo = sessionStorage.getItem('oldInfo');
    if(!!oldInfo){
        Stype = 'edit';
        oldInfo = JSON.parse(oldInfo);
        if(oldInfo.range === "1"){
            allInfo.range = 1;
            $('#Fanwei .btn_1').removeClass('btn-success');
            $('#Fanwei .btn_1').eq(1).addClass('btn-success');
            $('.Ren_').addClass('active');
            YuanBu = JSON.parse(oldInfo.user_department_object);
            YuanRen = JSON.parse(oldInfo.user_object);
            SetOut();
        }else{
            allInfo.range = 0;
            $('#Fanwei .btn_1').removeClass('btn-success');
            $('#Fanwei .btn_1').eq(0).addClass('btn-success');
        }
        $('#datetimepicker1 input').val(oldInfo.start_time);
        $('#datetimepicker2 input').val(oldInfo.end_time);
        $('#duration').val(oldInfo.duration_time);
        let notice = oldInfo.is_notice === '1'?true:false;
        let remind = oldInfo.is_remind === '1'?true:false;
        $('#notice')[0].checked = notice;
        $('#advancePush')[0].checked = remind;
        $('#passingScore').val(oldInfo.pass_mark);
        $('#explain').val(oldInfo.explain);
    }

    if(allInfo.type == 1){
        if(allInfo.all){
            if(Stype == 'edit' && oldInfo.items.length>0){
                allInfo.selList = oldInfo.items.map(el=>{
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
                allInfo.selList.forEach(el=>{
                    totalScore += parseInt(el.grade);
                })
                $('#Total').html(totalScore);
            }else{
                let info = {
                    ids:allInfo.ids
                }
                let url = baseIp + 'topics';
                $.ajax({
                    type:'post',
                    url,
                    data:JSON.stringify(info),
                    success:function(data){
                        console.log(data);
                        allInfo.selList = data;
                        allInfo.selList.forEach(el=>{
                            totalScore += parseInt(el.grade);
                        })
                        $('#Total').html(totalScore);
                    }
                })
            }

        }else{
            allInfo.selList.forEach(el=>{
                totalScore += parseInt(el.grade);
            })
        }

    }
    if(allInfo.type == 3){
        let topicArr = {};
        allInfo.rule.forEach((el,index)=>{
            let s = index + 1;
            topicArr[s] = {
                num:el.val,
                grade:el.score,
            }
        })
        let info = {
            ids:allInfo.ids,
            topicArr
        }
        let url = baseIp + 'random';
        // if(Stype == 'edit' && !!oldInfo.items && oldInfo.items.length>0){
        //     allInfo.selList = oldInfo.items.map(el=>{
        //         let ele = {};
        //         ele.id = el.item_id;
        //         ele.title = el.item_title;
        //         ele.type = el.item_type;
        //         ele.bank_id = el.bank_id;
        //         ele.bank_name = el.bank_name;
        //         ele.sort = el.sort;
        //         ele.grade = el.item_grade;
        //         ele.answer = el.item_answer;
        //         if(!!el.item_option){
        //             if(typeof(el.item_option) === 'string'){
        //                 el.item_option = JSON.parse(el.item_option);
        //             }
        //         }else{
        //             el.item_option = []
        //         }
        //         ele.options = el.item_option;
        //         return ele;
        //     });
        //     allInfo.selList.forEach(el=>{
        //         totalScore += parseInt(el.grade);
        //     })
        //     $('#Total').html(totalScore);
        // }else{
            $.ajax({
                type:'post',
                url,
                data:JSON.stringify(info),
                success:function(data){
                    let all = [];
                    allInfo.rule.forEach((el,index)=>{
                        let s = index + 1;
                        let item = data[s].items;
                        item = item.map(ele=>{
                            ele.grade = data[s].grade;
                            return ele;
                        })
                        all = all.concat(item);
                        return el;
                    })
                    allInfo.selList = all;
                    allInfo.selList.forEach(el=>{
                        totalScore += parseInt(el.grade);
                    })
                    $('#Total').html(totalScore);
                }
            })
        // }

    }
    if(allInfo.type == 2){
        allInfo.selList.forEach(el=>{
            totalScore += parseInt(el.grade);
        })
    }
    if(allInfo.range === 1){
        $('#Fanwei .btn_1').removeClass('btn-success');
        $('#Fanwei .btn_1').eq(1).addClass('btn-success');
        $('.Ren_').addClass('active');
    }

    $('#Total').html(totalScore);
    GetBu();
    function GetBu(){
        let url = baseIp + 'get-department-user-info';
        $.get(url,(data)=>{

            if(!!data['1']){
                if(!!allInfo.YuanBu){
                    YuanRen = allInfo.YuanRen;
                    NowBu = allInfo.YuanRen;
                    YuanBu = allInfo.YuanBu;
                    SetOut();
                }
                baseBu = data['1'];
                let str = SetTree(data['1'],'Base');
                $('.S_2_box_1').html(str);
                BindS();
            }
        },)
    }
    function BindS(){
        $('.tree_item_content').on('click',function(e){
            let next = $(this).next();
            if(!!next.attr('class')){
                next.slideToggle();
            }else{
                let id = $(this).data('id');
                let current = allOrganization.filter(el=>{return el.id == id});
                if(current.length>0 && !!current[0].userInfo){
                    currentList = current[0].userInfo;
                    SetRen(currentList);
                }else{
                    currentList = [];
                    SetRen([]);
                }
            }
        })
        $('.tree_item_content input').on('click',function(e){
            e.stopPropagation();
            let check = $(this)[0].checked;
            let next = $(this).parent().next().find('input');
            Array.from(next).forEach(el=>{
                el.checked = check;
            })
            let id = $(this).parent().data('id');
            let current = allOrganization.filter(el=>{return el.id == id});
            let all = [];
            let f = (list)=>{
                list.forEach(el=>{
                    if(check){
                        if(!NowBu.includes(el.id)){
                            NowBu.push(el.id);
                        }
                    }else{
                        let c = NowBu.indexOf(el.id);
                        if(c>-1){
                            NowBu.splice(c,1);
                        }
                    }
                    if(!!el.children&&el.children.length>0){
                        f(el.children);
                    }
                    all = all.concat(el.userInfo);
                })
            }
            f(current);
            if(check){
                let y = JSON.parse(JSON.stringify(SelRen));
                all.forEach(el=>{
                    let pan = false;
                    y.forEach(ele=>{
                        if(ele.userid == el.userid){
                            pan = true;
                        }
                    })
                    if(!pan){
                        SelRen.push(el);
                    }
                })
            }else{
                let y = JSON.parse(JSON.stringify(SelRen));
                all.forEach(el=>{
                    let pan = false;
                    let id = '';
                    y.forEach((ele,index)=>{
                        if(ele.userid == el.userid){
                            pan = true;
                            id = el.userid;
                        }
                    })
                    if(pan){
                        SelRen = SelRen.filter(ele=> ele.userid != id);
                    }
                })
            }
            SetInSelRen();
            console.log(SelRenId,'321')
            SetRen(currentList);
        })
    }

    function SearchRen(){
        let v = $('#Ren').val();
        let sel = allUser.filter(el=>el.name.indexOf(v)>-1);
        SetRen(sel);
    }
    function SearchBu(){
        let v = $('#Bumen').val();
        if(!!v){
            let s = {};
            for (let i = 0; i < allOrganization.length; i++) {
                const el = allOrganization[i];
                if(el.name.indexOf(v)>-1){
                    s = el;
                    break;
                }
            }
            if(!!s.id){
                let str = SetTree(s);
                $('.S_2_box_1').html(str);
                BindS();
                SetRen([]);
            }
        }else{
            let str = SetTree(baseBu);
            $('.S_2_box_1').html(str);
            BindS();
            SetRen([]);
        }
    }
    function SetRen(list){
        currentUser = list;
        let str = '';
        for (let i = 0; i < list.length; i++) {
            let el = list[i];
            let c = '';
            // if(SelRenId.includes(el.userid)) c = 'active'
            if(SelRenId.indexOf(el.userid+'')>-1) c = 'active'
            str += `<span style="cursor:pointer" data-id='${el.userid}' class='${c}'>${el.name}</span>`;
        }
        $('.S_2_box_2_2').html(str);

        $('.S_2_box_2_2 span').on('click',function(){
            let id = $(this).data('id');
            let ind = -1;
            for (let i = 0; i < SelRenId.length; i++) {
                let el = SelRenId[i];
                if(el == id){
                    ind = i;
                    break;
                }
            }
            if(ind>-1){
                SelRenId.splice(ind,1);
                SelRen.splice(ind,1);
            }else{
                let item = {};
                currentUser.forEach(function(el){
                    if(el.userid == id){
                        item = el;
                    }
                })
                SelRenId.push(id+'');
                SelRen.push(item);
            }
            $(this).toggleClass('active');
            SetInSelRen();
        })
    }
    function SetInSelRen(){
        let str = '';
        SelRenId = [];
        SelRen.forEach(el=>{
            str += `<span>${el.name}</span>`;
            SelRenId.push(el.userid);
        })
        $('.Sel_search .two').html(str);
    }

    function ClearRen() {
        YuanRen = [];
        SelRen = [];
        NowBu = [];
        YuanBu = [];
        Clear();
        $('.Ren_ .all_sel').html("");
    }


    function All(){
        let all = $('.S_2_box_2_2 span');
        Array.from(all).forEach((el,index)=>{
            let item = all.eq(index);
            if(item.attr('class')!='active'){
                item.addClass('active');
                let id = item.data('id');
                let item_ = currentUser.filter(ele=>ele.userid == id);
                SelRenId.push(id);
                SelRen.push(item_[0]);
            }
        })
        SetInSelRen();
    }
    function Fan(){
        let all = $('.S_2_box_2_2 span');
        Array.from(all).forEach((el,index)=>{
            let item = all.eq(index);
            if(item.attr('class')!='active'){
                item.addClass('active');
                let id = item.data('id');
                let item_ = currentUser.filter(ele=>ele.userid == id);
                SelRenId.push(id);
                SelRen.push(item_[0]);
            }else{
                item.removeClass('active');
                let id = item.data('id');
                let ind = SelRenId.indexOf(id);
                SelRenId.splice(ind,1);
                SelRen.splice(ind,1);
            }
        })
        SetInSelRen();
    }
    function Clear(){
        let all = $('.S_2_box_2_2 span');
        Array.from(all).forEach((el,index)=>{
            let item = all.eq(index);
            if(item.attr('class')!='active'){

            }else{
                item.removeClass('active');
                let id = item.data('id');
                let ind = SelRenId.indexOf(id);
                SelRenId.splice(ind,1);
                SelRen.splice(ind,1);
            }
        })
        SetInSelRen();
    }
    function SetTree(item,typeA){
        let type = typeof(item);
        if(!(item instanceof Array)){
            if(typeA == 'Base'){
                allOrganization.push(item);
            }
            let list = '';
            let x = '';
            if(!!item.children&&item.children.length>0){
                let c = SetTree(item.children,typeA);
                list = `<div class='tree_box'>${c}</div>`;
                x = `<img src="<?=Url::to('@web/img/paper/san.png')?>" alt="">`;
            }else{
                if(typeA == "Base") {
                    allUser = allUser.concat(item.userInfo);
                }
            }
            let c = '';
            if(NowBu.includes(item.id)){
                c = 'checked';
            }
            let str = `
                <div class='tree_item' >
                    <div class='tree_item_content' data-id='${item.id}'>
                        ${x}
                        <input type="checkbox" ${c}>
                        <span>${item.name}</span>
                    </div>
                    ${list}
                </div>
            `;
            return str;
        }else{
            let str = '';
            for (let i = 0; i < item.length; i++) {
                let el = item[i];
                let c =  SetTree(el,typeA);
                str += c;
            }
            return str;
        }
    }
    function Back(){
        window.history.back(-1);
    }
    function Next(type){
        if(allInfo.range === 0){

        }else if(allInfo.range === 1){
            if(YuanRen.length<=0){
                alert('请选择指定人员！');
                return false;
            }else{
                allInfo.YuanRen = YuanRen;
                allInfo.YuanBu = YuanBu;
            }
        }else{
            allInfo.range = 0;
        }
        allInfo.notice = $('#notice')[0].checked;
        let start = $('#datetimepicker1 input').val();
        let end = $('#datetimepicker2 input').val();
        if(!!!start){
            alert('请选择开始时间！');
            return false;
        }
        if(!!!end){
            alert('请选择结束时间！');
            return false;
        }
        start = new Date(start);
        end = new Date(end);
        if(start.getTime()>end.getTime()){
            alert('开始时间不能大于结束时间！');
            return false;
        }

        allInfo.startTime = GetFullTime(start);
        allInfo.endTime = GetFullTime(end);

        let duration = parseInt($('#duration').val());
        if(!!!duration){
            alert('请填写考试时长！');
            return false;
        }
        if(duration<=0){
            alert('考试时长不能为负数！');
            return false;
        }
        function isPInt(str) {
            var g = /^[1-9]*[1-9][0-9]*$/;
            return g.test(str);
        }

        if(!isPInt(duration)){
            alert('请填写正确格式的考试时长！');
            return false;
        }
        let jsStart = GetFullTime(start)+':00';
        let jsEnd = GetFullTime(end)+':00';
        jsStart = jsStart.replace(/\-/g, "/");
        jsEnd = jsEnd.replace(/\-/g, "/");
        let sTime =new Date(jsStart); //开始时间
        let eTime =new Date(jsEnd); //结束时间

        let timeDifference = parseInt(sTime - eTime) / 1000 / 60;
        if(-(timeDifference) < duration){
            alert('考试时长超出考试时间选择范围！');
            return false;
        }

        allInfo.duration = duration;
        allInfo.advancePush = $('#advancePush')[0].checked;

        let passingScore = parseInt($('#passingScore').val());
        if(passingScore<=0 || !!!passingScore){
            alert('请填写及格分数！');
            return false;
        }
        if(passingScore>totalScore){
            alert('及格分数请小于总分数！');
            return false;
        }
        allInfo.totalScore = totalScore;
        allInfo.passingScore = passingScore;

        let explain = $('#explain').val();
        if(!!!explain){
            alert('请填写考试说明!');
            return false;
        }
        allInfo.explain = explain;

        sessionStorage.setItem('allInfo',JSON.stringify(allInfo));
        if(type === 0){
            window.location.href = '<?=$preview?>';
        }
        if(type === 1){
            $.ajax({
                type: "POST",
                url: "<?=$url?>?status=0&id=<?=$id?>",
                data: JSON.stringify(allInfo),
                dataType: "json",
                success: function(data){
                    if(data.status == 1) {
                        layer.msg(data.msg, {time:1500}, function () {
                            window.location.href = "<?=$listUrl?>";
                        })
                    } else {
                        layer.msg(data.msg, {time:1500})
                    }
                }
            });
        }
        if(type === 2){
            $.ajax({
                type: "POST",
                url: "<?=$url?>?status=10&id=<?=$id?>",
                data: JSON.stringify(allInfo),
                dataType: "json",
                success: function(data){
                    if(data.status == 1) {
                        layer.msg(data.msg, {time:1500}, function () {
                            window.location.href = "<?=$listUrl?>";
                        })
                    } else {
                        layer.msg(data.msg, {time:1500})
                    }
                }
            });
        }
    }
    function GetFullTime(time) {
        time = new Date(time)
        let y = time.getFullYear()
        let M =
            time.getMonth() + 1 < 10
                ? '0' + (time.getMonth() + 1)
                : time.getMonth() + 1
        let d = time.getDate() < 10 ? '0' + time.getDate() : time.getDate()
        let h = time.getHours() < 10 ? '0' + time.getHours() : time.getHours()
        let m = time.getMinutes() < 10 ? '0' + time.getMinutes() : time.getMinutes()
        return y + '-' + M + '-' + d + ' ' + h + ':' + m
    }
    function Cancel(){
        $('.Sel_ren_').removeClass('active');
    }
    function Confirm(){
        YuanBu = JSON.parse(JSON.stringify(NowBu));
        YuanRen = JSON.parse(JSON.stringify(SelRen));
        SetOut();
        $('.Sel_ren_').removeClass('active');
    }
    function SetOut(){
        let str = YuanRen.map(el=>{
            return el.name;
        });
        str = str.join(',');
        $('.Ren_ .all_sel').html(str);
    }
    $('#datetimepicker1').datetimepicker({
        format: 'YYYY-MM-DD HH:mm:ss',
        // minDate: new Date(),
        locale: moment.locale('zh-cn')
    });
    $('#datetimepicker2').datetimepicker({
        format: 'YYYY-MM-DD HH:mm:ss',
        // minDate: new Date(),
        locale: moment.locale('zh-cn')
    });
    $('#Fanwei .btn_1').click(function(){
        $('#Fanwei .btn_1').removeClass('btn-success');
        $(this).addClass('btn-success');
        let ind = $('#Fanwei .btn_1').index($(this));
        allInfo.range = ind;
        if(ind === 0 ){
            $('.Ren_').removeClass('active');
        }else{
            $('.Ren_').addClass('active');
        }
    })
    $('#Sel_ren').click(function(){
        SelRen = YuanRen;
        SelRenId = YuanRen.map(el=>{
            return el.userid;
        })
        NowBu = JSON.parse(JSON.stringify(YuanBu));
        let str = SetTree(baseBu);
        $('.S_2_box_1').html(str);
        BindS();
        SetInSelRen();
        SetRen([]);
        $('.Sel_ren_').addClass('active');
    })
</script>
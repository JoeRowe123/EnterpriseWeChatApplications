<?php

use common\models\ArticleCategory;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Article */
/* @var $form yii\widgets\ActiveForm */
$this->registerJsFile(Url::to('@web/js/paper/jquery.min.js'), ['position' => $this::POS_HEAD]);
$this->registerJsFile(Url::to('@web/js/paper/less.min.js'), ['position' => $this::POS_END]);
?>
<head>
    <link rel="stylesheet/less" href="<?=Url::to('@web/css/paper/style.less')?>">
    <script src="https://cdn.bootcss.com/moment.js/2.22.0/moment-with-locales.js"></script>
    <script src="https://cdn.bootcss.com/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
</head>
<div class="article-form">

    <?php $form = ActiveForm::begin([
        'enableAjaxValidation' => true,
        'enableClientValidation' => true,
        'options' => [
            'class' => 'form-horizontal',
            "id" => "article-form",
            "onkeypress" => "if(event.keyCode==13)return false;"],
        'fieldConfig' => [
            'options' => ['class' => 'form-group'],
            'template' => "{label}\n<div class=\"col-sm-8\">{input}\n<span class=\"help-block m-b-none\">{error}</span></div><div class='col-sm-8 col-sm-offset-2'>{hint}</div>",
            'labelOptions' => ['class' => 'col-sm-2 control-label'],
        ]
    ]); ?>

    <?= $form->field($model, 'image')->widget(\common\widgets\cos\CosWidget::class, [
        'uploadUrl' => Yii::$app->params['cos']['uploadUrl'],
        'clientOptions' => [
            'max' => 1
        ]
    ])->hint("支持格式：image/jpeg,image/gif,image/png；(最多上传一张)") ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true, 'placeholder' => '请输入标题']) ?>

    <?= $form->field($model, 'abstract')->textarea(['maxlength' => true, 'placeholder' => '请输入内容摘要210字以内']) ?>

    <div class="form-group">
        <?= $form->field($model, 'first_category_id',[
            'options' => [
                'class' => ''
            ],
            'template' => "{label}\n<div class=\"col-sm-2\">{input}\n<span class=\"help-block m-b-none\">{error}</span></div>"
        ])->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\ArticleCategory::find()->where(['p_id' => 0, 'type' => $type, 'status' => 10])->asArray()->all(),'id','name'),[
            'prompt' => '请选择内容分类',
            'onchange'=>'$.get("/article/ajax-list",{id:$(this).val()},function(data){
                        if(data){
                            $("#article-second_category_id").html(data);
                            $("#article-third_category_id").html("<option value=0>请选择内容分类</option>");
                        }
            },"html")'
        ])->label('内容分类') ?>

        <?php if (!$model->isNewRecord) :?>
            <?= $form->field($model, 'second_category_id',[
                'options' => ['class' => ''],
                'template' => "<div class=\"col-sm-2\">{input}\n<span class=\"help-block m-b-none\">{error}</span></div>"
            ])->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\ArticleCategory::find()->where(['p_id' => $model->first_category_id, 'type' => $type, 'status' => 10])->asArray()->all(),'id','name'),[
                'prompt' => '请选择内容分类',
                'onchange'=>'$.get("/article/ajax-list",{id:$(this).val()},function(data){
                        if(data){
                            $("#article-third_category_id").html(data);
                        }
            },"html")'
            ]) ?>
        <?php else:?>
            <?= $form->field($model, 'second_category_id',[
                'options' => ['class' => ''],
                'template' => "<div class=\"col-sm-2\">{input}\n<span class=\"help-block m-b-none\">{error}</span></div>"
            ])->dropDownList([],[
                'prompt' => '请选择内容分类',
                'onchange'=>'$.get("/article/ajax-list",{id:$(this).val()},function(data){
                    if(data){
                        $("#article-third_category_id").html(data);
                    }
        },"html")'
            ]) ?>
        <?php endif;?>

        <?php if (!$model->isNewRecord) :?>
            <?= $form->field($model, 'third_category_id',[
                'options' => ['class' => ''],
                'template' => "<div class=\"col-sm-2\">{input}\n<span class=\"help-block m-b-none\">{error}</span></div>"
            ])->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\ArticleCategory::find()->where(['p_id'=>$model->second_category_id, 'type' => $type, 'status' => 10])->asArray()->all(),'id','name'),[
                'prompt' => '请选择内容分类',
            ]) ?>
        <?php else:?>
            <?= $form->field($model, 'third_category_id',[
                'options' => ['class' => ''],
                'template' => "<div class=\"col-sm-2\">{input}\n<span class=\"help-block m-b-none\">{error}</span></div>"
            ])->dropDownList([],[
                'prompt' => '请选择内容分类',
            ]) ?>

        <?php endif;?>

    </div>

    <?= $form->field($model, 'reading_time')->widget(\kartik\datetime\DateTimePicker::classname(), [
        'options' => ['placeholder' => '选择阅读期限', 'autocomplete' => 'off'],
        'pluginOptions' => [
            'autoclose' => true,
            'todayHighlight' => true,
            'format' => 'yyyy-mm-dd hh:ii:ss',
            'startDate' => date("Y-m-d H:i:s")
        ]
    ]); ?>

    <?= $form->field($model, 'is_push_msg')->radioList([0 => '否', 1 => '是']) ?>

    <div class="form-group field-article-read_object">
        <label class="col-sm-2 control-label" for="article-read_object">阅读对象</label>
        <div class="col-sm-8" id="Fanwei">
                <button type="button" class="btn btn_1 <?= (!$model->isNewRecord && $model->range == 0) || $model->isNewRecord ? 'btn-success' : '' ?>">全公司</button>
                <button type="button" class="btn btn_1 <?= !$model->isNewRecord && $model->range == 1 ? 'btn-success' : '' ?>">指定对象</button>
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

    <?= $form->field($model, 'is_secrecy')->radioList([0 => '否', 1 => '是']) ?>

    <?= $form->field($model, 'is_important_msg')->radioList([0 => '否', 1 => '是']) ?>

    <?= $form->field($model, 'content')->widget('kucha\ueditor\UEditor', [
        'clientOptions' => [
            'serverUrl' => \Yii::$app->params['cos']['uploadUeditUrl']
        ]
    ]); ?>

    <?= $form->field($model, 'attachment')->widget(\common\widgets\cos\CosWidget::class, [
        'uploadUrl' => Yii::$app->params['cos']['uploadUrl'],
        'clientOptions' => [
            'max' => 5,
            'accept' => 'application/pdf,application/vnd.ms-exce,application/vnd.ms-powerpoint,application/msword,text/plain,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.openxmlformats-officedocument.presentationml.presentation, application/vnd.ms-excel'
        ]
    ])->hint("最多可上传5个，仅支持doc、docx、xls、xlsx、ppt、pptx、pdf、txt类型文件") ?>

    <?php $statusArr = !$model->isNewRecord && $model->status == 10 ? [10 => '直接发布', 0 => '草稿'] : [10 => '直接发布', 0 => '草稿', 20 => '定时发布']?>
    <?= $form->field($model, 'status')->dropDownList($statusArr, ['id' => 'chose-status']) ?>

    <div class="timing_date" style="display: <?= !$model->isNewRecord && $model->status == 20 ? 'block' : 'none'?>">
        <?= $form->field($model, 'timing_date')->widget(\kartik\datetime\DateTimePicker::classname(), [
            'options' => ['placeholder' => '选择定时发布时间', 'autocomplete' => 'off'],
            'pluginOptions' => [
                'autoclose' => true,
                'todayHighlight' => true,
                'format' => 'yyyy-mm-dd hh:ii:ss',
                'startDate' => date("Y-m-d H:i:s")
            ]
        ]); ?>
    </div>

    <?= $form->field($model, 'read_object')->hiddenInput(["id" => "read_object"])->label("") ?>
    <?= $form->field($model, 'user_department_object')->hiddenInput(["id" => "user_department_object"])->label("") ?>
    <?= $form->field($model, 'range')->hiddenInput(["id" => "range", 'value' => 0])->label("") ?>


    <div class="form-group">
        <div class="col-sm-4 col-sm-offset-2">
            <?= Html::a('返回', $type == \common\models\Article::TYPE_TYKX ? ['index'] : ['list'], ['class' => 'btn btn-info']) ?>
            <?= Html::submitButton( '保存', ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<div class="Sel_ren_" style="z-index:9999;">
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
<?php
$isNew= $model->isNewRecord && !$id ? 1 : 0;
$oldInfo = $model->read_object ? $model->read_object : json_encode([]);
$oldDepartMentInfo = $model->user_department_object ? $model->user_department_object : json_encode([]);
$range = $model->isNewRecord  && !$id ? 0 : $model->range;
?>
<script>
    let baseIp = "<?=Yii::$app->params['backApi']?>";
    let SelRen = []; // 已选人员
    let SelRenId = [];// 已选人员 id
    let allOrganization = [];
    let allUser = [];
    let baseBu = {};
    let currentUser = [];
    let YuanRen = [];
    let YuanBu = [];
    let NowBu = [];
    let currentList = [];

    let range = <?=$range?>;
    let isNew = <?=$isNew?>;

    GetBu();
    function GetBu(){
        $("#range").val(range);
        YuanRen = isNew === 0 ? <?=$oldInfo?> : [];
        YuanBu = isNew === 0 ? <?=$oldDepartMentInfo?> : [];

        let url = baseIp + 'get-department-user-info';
        $.get(url,(data)=>{
            if(!!data['1']){
                if(!!YuanBu){
                    NowBu = YuanRen;
                    SetOut();
                }
                baseBu = data['1'];
                let str = SetTree(data['1'], 'Base');
                $('.S_2_box_1').html(str);
                BindS();
            }
        },)

        if(range === 0 ){
            $('#Fanwei .btn_1').removeClass('btn-success');
            $('#Fanwei .btn_1').eq(0).addClass('btn-success');
        }else{
            SelRen = YuanRen;
            SetInSelRen();
            SetOut();
            $('#Fanwei .btn_1').removeClass('btn-success');
            $('#Fanwei .btn_1').eq(1).addClass('btn-success');
            $('.Ren_').addClass('active');
        }
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
            if(SelRenId.includes(el.userid)) c = 'active'
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

    function ClearRen() {
        YuanRen = [];
        SelRen = [];
        YuanBu = [];
        NowBu = [];
        Clear();
        $('.Ren_ .all_sel').html("");
    }

    function SetTree(item, typeA){
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
                if(typeA == 'Base'){
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

    function Cancel(){
        $('.Sel_ren_').removeClass('active');
    }
    function Confirm(){
        YuanRen = JSON.parse(JSON.stringify(SelRen));
        YuanBu = JSON.parse(JSON.stringify(NowBu));
        SetOut();
        $('.Sel_ren_').removeClass('active');
    }
    function SetOut(){
        let str = YuanRen.map(el=>{
            return el.name;
        });
        str = str.join(',');
        $('.Ren_ .all_sel').html("");
        $('.Ren_ .all_sel').html(str);
        $("#read_object").val(JSON.stringify(YuanRen));
        $("#user_department_object").val(JSON.stringify(YuanBu));
    }
    $('#Fanwei .btn_1').click(function(){
        $('#Fanwei .btn_1').removeClass('btn-success');
        $(this).addClass('btn-success');
        let ind = $('#Fanwei .btn_1').index($(this));
        $("#range").val(ind);
        if(ind === 0){
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
        SetRen([]);
        $('.Sel_ren_').addClass('active');
    })


    $("#chose-status").on("change", function () {
        if($(this).val() == 20) {
            $(".timing_date").css("display", "block");
        } else {
            $(".timing_date").css("display", "none");
        }
    })
</script>
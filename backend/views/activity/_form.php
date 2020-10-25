<?php

use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Activity */
/* @var $form yii\widgets\ActiveForm */
$this->registerJsFile(Url::to('@web/js/paper/jquery.min.js'), ['position' => $this::POS_HEAD]);
$this->registerJsFile(Url::to('@web/js/paper/less.min.js'), ['position' => $this::POS_END]);
?>
<head>
    <link rel="stylesheet/less" href="<?=Url::to('@web/css/paper/style.less')?>">
    <script src="https://cdn.bootcss.com/moment.js/2.22.0/moment-with-locales.js"></script>
    <script src="https://cdn.bootcss.com/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
</head>

<div class="activity-form">

    <?php $form = ActiveForm::begin([
        'enableAjaxValidation' => true,
        'enableClientValidation' => true,
        'id' => 'dynamic-form',
        'options' => [
            'class' => 'form-horizontal',
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

    <?= $form->field($model, 'theme')->textInput(['maxlength' => true, 'placeholder' => '请输入活动主题']) ?>

    <?= $form->field($model, 'address')->textInput(['maxlength' => true, 'placeholder' => '请输入活动地点']) ?>

    <?= $form->field($model, 'start_time')->widget(\kartik\datetime\DateTimePicker::classname(), [
        'options' => ['placeholder' => '选择活动开始时间', 'autocomplete' => 'off'],
        'pluginOptions' => [
            'autoclose' => true,
            'todayHighlight' => true,
            'format' => 'yyyy-mm-dd hh:ii:ss',
            'startDate' => date("Y-m-d H:i:s")
        ]
    ]); ?>

    <?= $form->field($model, 'end_time')->widget(\kartik\datetime\DateTimePicker::classname(), [
        'options' => ['placeholder' => '选择活动结束时间', 'autocomplete' => 'off'],
        'pluginOptions' => [
            'autoclose' => true,
            'todayHighlight' => true,
            'format' => 'yyyy-mm-dd hh:ii:ss',
            'startDate' => date("Y-m-d H:i:s")
        ]
    ]); ?>

    <?= $form->field($model, 'close_date')->widget(\kartik\datetime\DateTimePicker::classname(), [
        'options' => ['placeholder' => '选择报名截止时间', 'autocomplete' => 'off'],
        'pluginOptions' => [
            'autoclose' => true,
            'todayHighlight' => true,
            'format' => 'yyyy-mm-dd hh:ii:ss',
            'startDate' => date("Y-m-d H:i:s")
        ]
    ]); ?>

    <?= $form->field($model, 'content')->widget('kucha\ueditor\UEditor', [
        'clientOptions' => [
            'serverUrl' => \Yii::$app->params['cos']['uploadUeditUrl']
        ]
    ]); ?>

    <div class="form-group field-activity-read_object">
        <label class="col-sm-2 control-label" for="activity-read_object">阅读对象</label>
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


    <div class="form-group field-fgj-area_id_2 required">
            <label class="col-sm-2 control-label" for="fgj-area_id_2">报名表单</label>
            <div class="col-sm-8">
                <?php
                DynamicFormWidget::begin([
                    'widgetContainer' => 'dynamicform_wrapper',
                    // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                    'widgetBody'      => '.container-items',
                    // required: css class selector
                    'widgetItem'      => '.form-item',
                    // required: css class
                    'min'             => 0,
                    // 0 or 1 (default 1)
                    'insertButton'    => '.add-item',
                    // css class
                    'deleteButton'    => '.remove-item',
                    // css class
                    'model'           => $modelAttr[0],
                    'formId'          => 'dynamic-form',
                    'formFields'      => [
                        'item_type',
                        'item_title',
                        'item_options',
                        'is_must'
                    ],
                ]);

                ?>
                <div class="panel panel-default ">
                    <div class="panel-heading">
                        <h4>
                            <i class="glyphicon glyphicon-list"></i>
                            <button type="button" class="add-item btn btn-success btn-sm pull-right"><i
                                        class="glyphicon glyphicon-plus"></i></button>
                        </h4>
                    </div>
                    <div class="panel-body">
                        <div class="container-items"><!-- widgetBody -->
                            <?php foreach ($modelAttr as $i => $item): ?>
                                <div class="form-item panel panel-default"><!-- widgetItem -->
                                    <div class="panel-heading">
                                        <h3 class="panel-title pull-left"></h3>
                                        <div class="pull-right">
                                            <button type="button" class="remove-item btn btn-danger btn-xs"><i
                                                        class="glyphicon glyphicon-minus"></i></button>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="panel-body">
                                        <?php
                                        if (!$item->isNewRecord) {
                                            echo Html::activeHiddenInput($item, "[{$i}]id");
                                        }
                                        ?>
                                        <?= $form->field($item, "[{$i}]item_type")->dropDownList(\common\models\ActivityItem::$type,['maxlength' => true, 'class' => "form-control item-type"]) ?>

                                        <?= $form->field($item, "[{$i}]item_title")->textInput(['maxlength' => true]) ?>

<!--                                        <div class="activity-option"-->
<!--                                             style="display: --><?php //echo (!$item->isNewRecord && $item->item_type != 3) || ($item->isNewRecord) ? 'block' : 'none' ?><!--">-->
                                        <div class="activity-option">
                                            <div class="form-group field-fgj-area_id_2 required">
                                                <label class="col-sm-2 control-label" for="fgj-area_id_2">选项</label>
                                                <div class="col-sm-8">
                                                    <?php
                                                    DynamicFormWidget::begin([
                                                        'widgetContainer' => 'dynamicform_wrapper_option',
                                                        // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                                                        'widgetBody'      => '.container-option-items',
                                                        // required: css class selector
                                                        'widgetItem'      => '.option-item',
                                                        // required: css class
                                                        'min'             => 1,
                                                        // 0 or 1 (default 1)
                                                        'insertButton'    => '.add-option-item',
                                                        // css class
                                                        'deleteButton'    => '.remove-option-item',
                                                        // css class
                                                        'model'           => $modelOptionAttr[0],
                                                        'formId'          => 'dynamic-form',
                                                        'formFields'      => [
                                                            'name',
                                                            'index'
                                                        ],
                                                    ]);

                                                    ?>
                                                    <div class="panel panel-default ">
                                                        <div class="panel-heading">
                                                            <h4>
                                                                <i class="glyphicon glyphicon-list"> 注：如果题目类型为问答题，可不填写此项 </i>
                                                                <button type="button" class="add-option-item btn btn-success btn-sm pull-right"><i
                                                                            class="glyphicon glyphicon-plus"></i></button>
                                                            </h4>
                                                        </div>
                                                        <div class="panel-body">
                                                            <div class="container-option-items"><!-- widgetBody -->
                                                                <?php foreach ($modelOptionAttr as $k => $attr): ?>
                                                                    <div class="option-item panel panel-default" style="display: <?=(!$item->isNewRecord && $item->item_type != 3 && isset($item->item_options[$k]) || $item->isNewRecord) ? 'block' : 'none'?>"><!-- widgetItem -->
                                                                        <div class="panel-heading">
                                                                            <h3 class="panel-title pull-left"></h3>
                                                                            <div class="pull-right">
                                                                                <button type="button" class="remove-option-item btn btn-danger btn-xs"><i
                                                                                            class="glyphicon glyphicon-minus"></i></button>
                                                                            </div>
                                                                            <div class="clearfix"></div>
                                                                        </div>
                                                                        <div class="panel-body">
                                                                            <?= $form->field($attr, "[{$i}]name[]")->textarea(['maxlength' => true, 'value' => !$item->isNewRecord && $item->item_type != 3 ? $item->item_options[$k] ?? "" : '']) ?>
                                                                        </div>

                                                                    </div>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        </div>

                                                    </div><!-- .panel -->
                                                    <?php DynamicFormWidget::end(); ?>
                                                </div>
                                            </div>
                                        </div>

                                        <?= $form->field($item, "[{$i}]is_must")->dropDownList([-1 => '选答', 1 => '必答'],['maxlength' => true])?>
                                    </div>

                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                </div><!-- .panel -->
                <?php DynamicFormWidget::end(); ?>
        </div>
    </div>



    <?= $form->field($model, 'limit_person_num')->textInput(['maxlength' => true, 'placeholder' => '请填写上限人数，不填则为不限制']) ?>

    <?= $form->field($model, 'is_push_msg')->radioList([0 => '不推送消息仅发布', 1=> '推送消息']) ?>

    <?= $form->field($model, 'status')->dropDownList([10 => '直接发布', 0 => '置为草稿']) ?>

    <?= $form->field($model, 'read_object')->hiddenInput(["id" => "read_object"])->label("") ?>
    <?= $form->field($model, 'user_department_object')->hiddenInput(["id" => "user_department_object"])->label("") ?>
    <?= $form->field($model, 'range')->hiddenInput(["id" => "range", 'value' => 0])->label("") ?>

    <div class="form-group">
        <div class="col-sm-4 col-sm-offset-2">
            <?= Html::a('返回', ['index'], ['class' => 'btn btn-info']) ?>
            <?= Html::submitButton($model->isNewRecord ? '保存' : '保存', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
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
$isNew= $model->isNewRecord ? 1 : 0;
$oldInfo = $model->read_object ? $model->read_object : json_encode([]);
$oldDepartMentInfo = $model->user_department_object ? $model->user_department_object : json_encode([]);
$range = $model->isNewRecord ? 0 : $model->range;
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
</script>

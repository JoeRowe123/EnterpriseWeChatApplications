<?php

use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Vote */
/* @var $form yii\widgets\ActiveForm */
$this->registerJsFile(Url::to('@web/js/paper/jquery.min.js'), ['position' => $this::POS_HEAD]);
$this->registerJsFile(Url::to('@web/js/paper/less.min.js'), ['position' => $this::POS_END]);
?>
<head>
    <link rel="stylesheet/less" href="<?=Url::to('@web/css/paper/style.less')?>">
    <script src="https://cdn.bootcss.com/moment.js/2.22.0/moment-with-locales.js"></script>
    <script src="https://cdn.bootcss.com/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
</head>

<div class="vote-form">

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


    <?= $form->field($model, 'title')->textInput(['maxlength' => true, 'placeholder' => '请输入投票标题']) ?>

    <?= $form->field($model, 'type')->dropDownList([2=> '文字投票', 1 => '图文投票'], ['id' => 'type']) ?>

    <div class="options"
         style="display: <?= (!$model->isNewRecord && $model->type == \common\models\QuestionBankItem::TYPE_GAP_FILLING) || (!$model->isNewRecord && $model->type == \common\models\QuestionBankItem::TYPE_JUDGE) ? 'none' : 'block' ?>">
        <div class="form-group field-fgj-area_id_2 required">
            <label class="col-sm-2 control-label" for="fgj-area_id_2">选项</label>
            <div class="col-sm-8">
                <?php
                DynamicFormWidget::begin([
                    'widgetContainer' => 'dynamicform_wrapper',
                    // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                    'widgetBody'      => '.container-items',
                    // required: css class selector
                    'widgetItem'      => '.item',
                    // the maximum times, an element can be cloned (default 999)
                    'min'             => 1,
                    // 0 or 1 (default 1)
                    'insertButton'    => '.add-item',
                    // css class
                    'deleteButton'    => '.remove-item',
                    // css class
                    'model'           => $modelAttr[0],
                    'formId'          => 'dynamic-form',
                    'formFields'      => [
                        'option_name',
                        'option_image'
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
                                <div class="item panel panel-default"><!-- widgetItem -->
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
                                        <div class="vote-option"
                                             style="display: <?= (!$model->isNewRecord && $model->type == 1) ? 'block' : 'none' ?>">
                                        <?php
//                                        echo $form->field($item, "[{$i}]option_image")->widget(\common\widgets\cos\CosWidget::class, [
//                                            'uploadUrl' => Yii::$app->params['cos']['uploadUrl'],
//                                            'clientOptions' => [
//                                                'max' => 1
//                                            ]
//                                        ])->hint("支持格式：image/jpeg,image/gif,image/png");
                                        echo $form->field($item, "[{$i}]option_image")->widget('manks\FileInput')->hint("支持格式：image/jpeg,image/gif,image/png");
                                        ?>
                                        </div>
                                        <?= $form->field($item, "[{$i}]option_name")->textInput(['maxlength' => true, '']) ?>
                                        <?= $form->field($item, "[{$i}]type")->hiddenInput(['maxlength' => true, 'class' => 'optionType', 'value' => 1])->label("") ?>
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

    <div>
        <h3 style="margin-left: 120px;margin-bottom: 15px;">投票设置</h3>
        <?= $form->field($model, 'start_time')->widget(\kartik\datetime\DateTimePicker::classname(), [
            'options' => ['placeholder' => '选择投票开始时间', 'autocomplete' => 'off'],
            'pluginOptions' => [
                'autoclose' => true,
                'todayHighlight' => true,
                'format' => 'yyyy-mm-dd hh:ii:ss',
            ]
        ]); ?>

        <?= $form->field($model, 'end_time')->widget(\kartik\datetime\DateTimePicker::classname(), [
            'options' => ['placeholder' => '选择投票结束时间', 'autocomplete' => 'off'],
            'pluginOptions' => [
                'autoclose' => true,
                'todayHighlight' => true,
                'format' => 'yyyy-mm-dd hh:ii:ss',
            ]
        ]); ?>

        <?= $form->field($model, 'option_type')->dropDownList([1 => '单选', 2=> '多选'], ['id' => 'option-type', 'disabled' => !$model->isNewRecord && $model->status == \common\models\Vote::STATUS_GOING ? true : false]) ?>

        <div class="option-num"
             style="display: <?= !$model->isNewRecord && $model->option_type == 2 ? 'block' : 'none' ?>">
            <?= $form->field($model, 'multiple_num')->textInput(['maxlength' => true, 'placeholder' => '请输入每人最多可投票数', 'disabled' => !$model->isNewRecord && $model->status == \common\models\Vote::STATUS_GOING ? true : false]) ?>
        </div>
        <?= $form->field($model, 'vote_type')->dropDownList([1 => '实名投票', 2=> '匿名投票'], ['disabled' => !$model->isNewRecord && $model->status == \common\models\Vote::STATUS_GOING ? true : false]) ?>

        <?= $form->field($model, 'is_repetition')->dropDownList([0 => '否', 1=> '是'], ['disabled' => !$model->isNewRecord && $model->status == \common\models\Vote::STATUS_GOING ? true : false]) ?>

        <div class="form-group field-vote-read_object">
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

        <?= $form->field($model, 'is_view')->dropDownList([0 => '否', 1=> '是'], ['disabled' => !$model->isNewRecord && $model->status == \common\models\Vote::STATUS_GOING ? true : false]) ?>

        <?= $form->field($model, 'is_notice')->radioList([0 => '不推送消息仅发布', 1=> '推送消息']) ?>

        <?= $form->field($model, 'status')->dropDownList([20 => '直接发布', 0 => '置为草稿']) ?>
        <?= $form->field($model, 'read_object')->hiddenInput(["id" => "read_object"])->label("") ?>
        <?= $form->field($model, 'user_department_object')->hiddenInput(["id" => "user_department_object"])->label("") ?>
        <?= $form->field($model, 'range')->hiddenInput(["id" => "range", 'value' => 0])->label("") ?>
    </div>



    <div class="form-group">
        <div class="col-sm-4 col-sm-offset-2">
            <?= Html::a('返回', ['index'], ['class' => 'btn btn-info']) ?>
            <?= Html::submitButton($model->isNewRecord ? '保存' : '保存', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

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
<?php
$isNew= $model->isNewRecord ? 1 : 0;
$oldInfo = $model->read_object ? $model->read_object : json_encode([]);
$oldDepartMentInfo = $model->user_department_object ? $model->user_department_object : json_encode([]);
$range = $model->isNewRecord ? 0 : $model->range;
$url = Yii::$app->params['cos']['uploadUrl'];
$js = <<<JS
    $(function() {
       var type_v = $("#type").val();
      if(type_v == 1) {
            $(".vote-option").css("display", "block");
             $(".optionType").attr("value","1");
       } else {
            $(".vote-option").css("display", "none");
             $(".optionType").attr("value","2");
       }
    });

   $("#type").change(function() {
       if($(this).val() == 1) {
            $(".vote-option").css("display", "block");
             $(".optionType").attr("value","1");
       } else {
            $(".vote-option").css("display", "none");
             $(".optionType").attr("value","2");
       }
   })
   
    $("#option-type").change(function() {
       if($(this).val() == 2) {
            $(".option-num").css("display", "block");
       } else {
            $(".option-num").css("display", "none");
       }
   })
   
   $(".dynamicform_wrapper").on("afterInsert", function(e, item) {
       $('img.cus-img').eq($('img.cus-img').length-1).attr('src',webupload_config_webupload_00000000.defaultImage);
      $('#webupload_config_webupload_00000000').webupload_fileinput(webupload_config_webupload_00000000)
    if($("#type").val() == 1) {
            $(".vote-option").css("display", "block");
             $(".optionType").attr("value","1");
       } else {
            $(".vote-option").css("display", "none");
             $(".optionType").attr("value","2");
       }
    });
   
   $(".dynamicform_wrapper").on("afterDelete", function(e, item) {
    i--;
    if($("#type").val() == 1) {
            $(".vote-option").css("display", "block");
             $(".optionType").attr("value","1");
       } else {
            $(".vote-option").css("display", "none");
             $(".optionType").attr("value","2");
       }
    });
JS;
$this->registerJs($js);
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

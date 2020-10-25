<?php
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

\backend\assets\LoginAssset::register($this);
$this->title = 'login'
?>

<?php $this->beginPage() ?>
    <!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
<body class="gray-bg">
<?php $this->beginBody() ?>
<?php if ($type == 'username'):?>
    <div class="middle-box text-center loginscreen  animated fadeInDown">
        <div>
            <div>

                <h1 class="logo-name">XY</h1>

            </div>
            <h3>欢迎使用 </h3>
            <?php $form = ActiveForm::begin(['id' => 'login-form', 'options' => ['class' => 'm-t']]); ?>
            <div class="form-group">
                <?= $form->field($model, 'username')
                    ->textInput(['autofocus' => true, 'placeholder' => "用户名"])
                    ->label(false) ?>
            </div>
            <div class="form-group">
                <?= $form->field($model, 'password')
                    ->passwordInput(['autofocus' => false, 'placeholder' => "密码"])
                    ->label(false) ?>
            </div>
            <?= Html::submitButton('登录', [
                'class' => 'btn btn-primary block full-width m-b',
                'id'    => 'loginBtn',
                'name'  => 'login-button'
            ]) ?>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
<?php else:?>
    <div class="middle-box text-center loginscreen  animated fadeInDown">
        <div>
            <div>

                <h2>信谊综合办</h2>

            </div>
            <h3>欢迎使用</h3>
                <div id="wx_reg"></div>
            </a>
        </div>
    </div>
<?php endif;?>
<?php $this->endBody() ?>
<?php
    $url = urlencode("http://zhb.sinety.cn/site/wework-login");
    $state = uniqid();
    Yii::$app->session->set("qy-state", $state);
    $js = <<<JS
$(function() {
    !function(a,b,c){function d(c){var d=b.createElement("iframe"),e="https://open.work.weixin.qq.com/wwopen/sso/qrConnect?appid="+c.appid+"&agentid="+c.agentid+"&redirect_uri="+c.redirect_uri+"&state="+c.state+"&login_type=jssdk";e+=c.style?"&style="+c.style:"",e+=c.href?"&href="+c.href:"",d.src=e,d.frameBorder="0",d.allowTransparency="true",d.scrolling="no",d.width="300px",d.height="400px";var f=b.getElementById(c.id);f.innerHTML="",f.appendChild(d),d.onload=function(){d.contentWindow.postMessage&&a.addEventListener&&(a.addEventListener("message",function(b){
b.data&&b.origin.indexOf("work.weixin.qq.com")>-1&&(a.location.href=b.data)}),d.contentWindow.postMessage("ask_usePostMessage","*"))}}a.WwLogin=d}(window,document);
    
  window.WwLogin({
        "id" : "wx_reg",  
        "appid" : "wxcd0971fc49bd8ea6",
        "agentid" : "1000004",
        "redirect_uri" :"$url",
        "state" : "{$state}",
        "href" : "",
});
})
JS;
    $this->registerJs($js);
    ?>
</body>
</html>
<?php $this->endPage() ?>




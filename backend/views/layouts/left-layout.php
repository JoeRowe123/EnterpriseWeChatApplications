<?php

/* @var $this \yii\web\View */
/* @var $content string */

use backend\assets\AdminLeftAssset;
use yii\helpers\Html;
$this->title = '管理员后台';

AdminLeftAssset::register($this);
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
<body class="fixed-sidebar full-height-layout gray-bg" style="overflow:hidden">
<?php $this->beginBody() ?>
<div id="wrapper">
    <!--左侧导航开始-->
    <nav class="navbar-default navbar-static-side" role="navigation">
        <div class="nav-close"><i class="fa fa-times-circle"></i>
        </div>
        <div class="sidebar-collapse">
            <ul class="nav" id="side-menu">
                <li class="nav-header">
                    <div class="dropdown profile-element">
                        <span><img alt="image" class="img-circle" src="<?="img/user.jpeg"?>" width="64" height="64"/></span>
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                <span class="clear">
                                    <span class="block m-t-xs"><strong class="font-bold"><?= Yii::$app->user->identity->name ?></strong></span>
                                </span>
                        </a>
                    </div>
                    <div class="logo-element">信谊
                    </div>
                </li>

                <?php
                foreach ($this->params['menu'] as $var) {
                    $me = '<li>
                            <a href="#">
                                <i class="' . $var['class'] . '"></i>
                                <span class="nav-label">' . $var['name'] . '</span>
                                <span class="fa arrow"></span>
                            </a>';
                    echo $me;
                    $li = '';
                    foreach ($var['child'] as $child) {
                        $li .='<li>
                                <a class="J_menuItem" href="' . $child['url'][0] . '" data-index="0">' . $child['name'] . '</a>
                              </li>';
                    }
                    $ul = '<ul class="nav nav-second-level">' . $li . '</ul>
                           </li>';
                    echo $ul;
                }
                ?>

            </ul>
        </div>
    </nav>
    <!--左侧导航结束-->
    <!--右侧部分开始-->
    <div id="page-wrapper" class="gray-bg dashbard-1">
        <div class="row border-bottom">
            <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
                <div class="navbar-header"><a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
                </div>
            </nav>
        </div>
        <div class="row content-tabs">
            <button class="roll-nav roll-left J_tabLeft"><i class="fa fa-backward"></i>
            </button>
            <nav class="page-tabs J_menuTabs">
                <div class="page-tabs-content">
                    <a href="javascript:;" class="active J_menuTab" data-id="index_v1.html">首页</a>
                </div>
            </nav>
            <button class="roll-nav roll-right J_tabRight"><i class="fa fa-forward"></i>
            </button>
            <div class="btn-group roll-nav roll-right">
                <button class="dropdown J_tabClose" data-toggle="dropdown">关闭操作<span class="caret"></span>

                </button>
                <ul role="menu" class="dropdown-menu dropdown-menu-right">
                    <li class="J_tabShowActive"><a>定位当前选项卡</a>
                    </li>
                    <li class="divider"></li>
                    <li class="J_tabCloseAll"><a>关闭全部选项卡</a>
                    </li>
                    <li class="J_tabCloseOther"><a>关闭其他选项卡</a>
                    </li>
                </ul>
            </div>
            <a href="<?=\yii\helpers\Url::toRoute('site/logout')?>" class="roll-nav roll-right J_tabExit"><i class="fa fa fa-sign-out"></i> 退出</a>
        </div>
        <div class="row J_mainContent" id="content-main">
            <iframe class="J_iframe" name="iframe0" width="100%" height="100%" src="/site/info" frameborder="0" data-id="index_v1.html" seamless></iframe>
        </div>
        <div class="footer">
            <div class="pull-right">&copy; 2017-2018</div>
        </div>
    </div>
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

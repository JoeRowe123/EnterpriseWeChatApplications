<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/12/8
 * Time: 23:52
 */

namespace frontend\controllers;


class UserController extends BaseController
{
    public function actionUserInfo()
    {
        $userInfo['uid'] = \Yii::$app->session->get('uid');
        $userInfo['userid'] = \Yii::$app->session->get('userid');
        $userInfo['username'] = \Yii::$app->session->get('username');
        $userInfo['avatar'] = \Yii::$app->session->get('avatar');
        return $userInfo;
    }
}
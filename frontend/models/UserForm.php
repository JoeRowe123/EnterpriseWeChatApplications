<?php
/**
 * Copyright(c) 2018-2050,BWSC.Co.Ltd.
 * Created by PhpStorm.
 * User: JoeRowe
 * Date: 2019/11/28 0028
 * Time: 9:52
 *
 */

namespace frontend\models;


use common\models\User;

class UserForm extends User
{
    public function signUp($usrInfo)
    {
        $usr = User::findOne(["userid" => $usrInfo["userid"]]);
        if ($usr)
        {
            \Yii::$app->session->set('uid', $usr['id']);
            \Yii::$app->session->set('userid', $usr['userid']);
            \Yii::$app->session->set('username', $usr['name']);
            \Yii::$app->session->set('avatar', $usr['avatar']);
        }else{
            $user = new User();
            $user->load($usrInfo);
            $user->userid = $usrInfo["userid"];
            $user->avatar = $usrInfo["avatar"];
            $user->password_hash = \Yii::$app->security->generatePasswordHash($usrInfo["mobile"]);
            $user->generateAuthKey();
            $user->name = $usrInfo["name"];
            $user->username = $usrInfo["name"];
            $user->email = $usrInfo["email"];
            $user->position = $usrInfo["position"];
            $user->department = $usrInfo["department"];
            $user->mobile = $usrInfo["mobile"];
            $user->gender = $usrInfo["gender"];
            $user->created_at = time();
            $user->updated_at = time();
            if ($user->save()){
                \Yii::$app->session->set('uid', \Yii::$app->db->getLastInsertID());
                \Yii::$app->session->set('userid', $usrInfo['userid']);
                \Yii::$app->session->set('username', $usrInfo['name']);
                \Yii::$app->session->set('avatar', $usrInfo['avatar']);
            }else{
                return false;
            }
        }
        return true;
    }
}
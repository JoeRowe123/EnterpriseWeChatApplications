<?php
/**
 * Copyright(c) 2018-2050,BWSC.Co.Ltd.
 * Created by PhpStorm.
 * User: JoeRowe
 * Date: 2019/11/29 0029
 * Time: 11:35
 *
 */

namespace frontend\behaviors;


use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\web\HttpException;

class UserAuthBehaviors extends Behavior
{
    public function events()
    {
        return [];
    }

    public function beforeValidate($event)
    {
        if (!\Yii::$app->session->has('userid') || 1)
        {
            throw new HttpException(403, "请先登录");
        }
        return true;
    }
}
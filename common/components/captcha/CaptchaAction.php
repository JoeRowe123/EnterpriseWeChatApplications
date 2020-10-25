<?php

namespace common\components\captcha;

use common\helpers\StringHelper;

class CaptchaAction extends \yii\captcha\CaptchaAction
{
    public function run()
    {
        $key = StringHelper::generateSn("code");
        \Yii::$app->cache->set($key,$this->getVerifyCode(),300);
        \Yii::$app->getResponse()->getHeaders()
            ->set('captcha-key', $key);
        return parent::run(); // TODO: Change the autogenerated stub
    }
}
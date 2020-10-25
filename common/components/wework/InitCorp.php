<?php
/**
 * Created by PhpStorm.
 * User: MrDong
 * Date: 2019/11/18
 * Time: 11:13
 */
namespace common\components\wework;

include_once("api/src/CorpAPI.class.php");

class InitCorp
{
    /**
     * @return \CorpAPI
     */
    public static function init()
    {
        $wework = new \CorpAPI(\Yii::$app->params['wework']['corpId'], \Yii::$app->params['wework']['secret']);
        return $wework;
    }
}
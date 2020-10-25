<?php
/**
 * Created by PhpStorm.
 * User: MrDong
 * Date: 2019/11/29
 * Time: 10:59
 */

namespace common\components\wework;

use Message;
use TextCardMessageContent;
use yii\db\Exception;

include_once("api/src/CorpAPI.class.php");
include_once("api/src/ServiceCorpAPI.class.php");
include_once("api/src/ServiceProviderAPI.class.php");

class WeworkSendMsg
{
    /**
     * 企业微信文本卡片消息消息发送
     * @param $touser array 企业微信用户
     * @param $toparty array 部门
     * @param $totag array 标签
     * @param $params array 发送消息内容
     * @param bool $isAll 是否发送全公司
     * @param int $isSafe 是否保密消息
     * @return bool
     * @throws Exception
     */
    public static function send($touser, $toparty, $totag, $params, $isAll = false, $isSafe = 0)
    {
        try {
            $message = new Message();
            {
                $message->sendToAll = $isAll;
                $message->touser = $touser;
                $message->toparty = $toparty;
                $message->totag= $totag;
                $message->agentid = \Yii::$app->params['wework']['frontId'];
                $message->safe = $isSafe;

                $message->messageContent = new TextCardMessageContent(
                    $params['title'], $params['description'], $params['url'], $params['btntxt']
                );
            }
            $api = new \CorpAPI(\Yii::$app->params['wework']['corpId'], \Yii::$app->params['wework']['frontSecret']);
            $invalidUserIdList = null;
            $invalidPartyIdList = null;
            $invalidTagIdList = null;

            $api->MessageSend($message, $invalidUserIdList, $invalidPartyIdList, $invalidTagIdList);
            return true;
        }catch (\Exception $e) {
            \Yii::error("模板消息发送失败：".$e->getMessage());
            throw new Exception("模板消息发送失败：".$e->getMessage());
        }

    }
}
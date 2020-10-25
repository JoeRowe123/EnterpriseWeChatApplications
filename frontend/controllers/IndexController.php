<?php
/**
 * Copyright(c) 2018-2050,BWSC.Co.Ltd.
 * Created by PhpStorm.
 * User: JoeRowe
 * Date: 2019/11/6 0006
 * Time: 11:38
 *
 */

namespace frontend\controllers;


use common\models\User;
use frontend\models\UserForm;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\HttpException;

include_once("../../common/components/wework/api/src/CorpAPI.class.php");

class IndexController extends Controller
{
    /**
     * @return mixed
     */
    public function actionGetUserInfo()
    {
        //通过code获得openid
        if (!\Yii::$app->request->get('code')) {
            //触发微信返回code码
            $baseUrl = urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . $_SERVER['QUERY_STRING']);
            if (\Yii::$app->request->get('id')) {
                $baseUrl .= "&type=" . \Yii::$app->request->get('type') . "&id=" . \Yii::$app->request->get('id');
            }
            if (\Yii::$app->request->get('back_url')) {
                $baseUrl .= \Yii::$app->request->get('back_url');
            }
            $url = $this->_CreateOauthUrlForCode($baseUrl);
            header("Location:" . $url);
            exit();
        } else {
            $accessTokenInfo = $this->getAccessToken();
            if ($accessTokenInfo["errcode"]) {
                return $this->redirect("/html/index.html#/none");
            }
            $accessToken = $accessTokenInfo["access_token"];
            $userInfo = $this->actionSignByCode($accessToken, \Yii::$app->request->get("code"));
            if (isset($userInfo["errcode"])&&$userInfo["errcode"]) {
                return $this->redirect("/html/index.html#/none");
            }
            if ($userInfo) {
                $baseUrl = "/html/index.html";
                if (\Yii::$app->request->get('id')) {
                    $baseUrl .= "?type=" . \Yii::$app->request->get('type') . "&id=" . \Yii::$app->request->get('id');
                }
                if (\Yii::$app->request->get('back_url')) {
                    $baseUrl .= \Yii::$app->request->get('back_url');
                }
                return $this->redirect($baseUrl);
            }
        }
    }

    /**
     * @param $accessToken
     * @param $code
     * @return mixed
     * @throws HttpException
     */
    public function actionSignByCode($accessToken, $code)
    {
        $userBase = $this->getUserId($accessToken, $code);
        if ($userBase["errcode"]||!isset($userBase['UserId'])) {
            return ["errcode"=>1];
        }
        $userId = $userBase['UserId'];
        $userInfo = $this->getUserInfoByUserID($accessToken, $userId);
        if ($userInfo['errcode']) {
            return ["errcode"=>1];
        }
        $user = new UserForm();
        if ($user->signUp($userInfo)) {
            return $userInfo;
        }
        return ["errcode"=>1];
    }

    /**js-sdk所需参数
     * @param $url
     * @return array
     */
    public function actionCfgInfo()
    {
        $url = urldecode(\Yii::$app->request->post('url'));
        $timestamp = time();
        $nonceStr = self::getNonceStr();
        $signature = $this->getSignature($url, $nonceStr, $timestamp);
        return [
            "appId" => \Yii::$app->params["wework"]['corpId'],
            "timestamp" => $timestamp,
            "nonceStr" => $nonceStr,
            "signature" => $signature
        ];
    }

    /**
     * @param $accessToken
     * @param $userId
     * @return mixed
     */
    public function getUserInfoByUserID($accessToken, $userId)
    {
        $urlObj["access_token"] = $accessToken;
        $urlObj["userid"] = "$userId";
        $bizString = "https://qyapi.weixin.qq.com/cgi-bin/user/get?" . $this->ToUrlParams($urlObj);
        $userInfo = file_get_contents($bizString);
        return json_decode($userInfo, true);
    }

    /**
     * @return bool|mixed|string
     */
    public function getAccessToken()
    {
        $url = $this->getTokenUrl();
        $info = file_get_contents($url);
        $info = json_decode($info, true);
        return $info;
    }

    /**
     * @param $access_token
     * @param $code
     * @return bool|mixed|string
     */
    public function getUserId($access_token, $code)
    {
        $url = "https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo?access_token=$access_token&code=$code";
        $userBase = file_get_contents($url);
        $userBase = json_decode($userBase, true);
        if (!isset($userBase['UserId'])) {
            if (!isset($userBase['OpenId'])){
                return ["errcode"=>1];
            }
            $userBase['UserId'] = $this->openID2UserId($access_token, $userBase['OpenId']);
        }
        return $userBase;
    }

    public function openID2UserId($access_token, $openID)
    {
        $url = "https://qyapi.weixin.qq.com/cgi-bin/user/convert_to_userid?access_token=$access_token";
        $userID = $this->postUrl($url, json_encode(['openid' => $openID]));
        $userID = json_decode($userID, true);
        if (!isset($userID["userid"])) {
            return ["errcode"=>1];
        }
        return $userID['userid'];
    }

    /**生成js-sdk签名
     * @param $url
     * @param $nonceStr
     * @param $timestamp
     * @return string
     */
    public function getSignature($url, $nonceStr, $timestamp)
    {
        if (!\Yii::$app->session->has('jsapi_ticket'))
        {
            $ticket = $this->getTicket();
            \Yii::$app->session->set('jsapi_ticket', ['ticket'=>$ticket,'time'=>time()]);
        }
        $ticketInfo = \Yii::$app->session->get('jsapi_ticket');
        $ticket = $ticketInfo['ticket'];
        if ((time() - $ticketInfo["time"]) > 7200)
        {
            $ticket = $this->getTicket();
            \Yii::$app->session->set('jsapi_ticket', ['ticket'=>$ticket,'time'=>time()]);
        }
        //获取signature
        $signature = sha1("jsapi_ticket=" . $ticket . "&noncestr=" . $nonceStr . "&timestamp=" . $timestamp . "&url=" .$url);
        return $signature;
    }

    /**
     * @return mixed
     * @throws HttpException
     */
    public function getTicket()
    {
        if (!\Yii::$app->session->has('usr_token'))
        {
            $token_data = $this->getAccessToken();
            \Yii::$app->session->set('usr_token', ['access_token'=>$token_data['access_token'],'time'=>time()]);
        }
        $token = \Yii::$app->session->get('usr_token');
        if ((time() - $token["time"]) > 7200)
        {
            $token_data = $this->getAccessToken();
            \Yii::$app->session->set('usr_token', ['access_token'=>$token_data['access_token'],'time'=>time()]);
        }
        $data = file_get_contents("https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=" . $token["access_token"]);
        $data = json_decode($data, true);
        if ($data['errcode']) {
            return $data;
        }
        return $data['ticket'];
    }

    /**
     *
     * 产生随机字符串，不长于32位
     * @param int $length
     * @return 产生的随机字符串
     */
    public static function getNonceStr($length = 32)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str ="";
        for ( $i = 0; $i < $length; $i++ )  {
            $str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }
        return $str;
    }

    public function getTokenUrl()
    {
        $urlObj["corpid"] = \Yii::$app->params["wework"]['corpId'];
        $urlObj["corpsecret"] = \Yii::$app->params["wework"]['frontSecret'];
        $bizString = $this->ToUrlParams($urlObj);
        return "https://qyapi.weixin.qq.com/cgi-bin/gettoken?" . $bizString;
    }

    /**
     * @param $redirectUrl
     * @return string
     */
    public function _CreateOauthUrlForCode($redirectUrl)
    {
        $urlObj["appid"] = \Yii::$app->params['wework']['corpId'];
        $urlObj["redirect_uri"] = "$redirectUrl";
        $urlObj["response_type"] = "code";
        $urlObj["scope"] = "snsapi_base";
        $urlObj["state"] = "STATE" . "#wechat_redirect";
        $bizString = $this->ToUrlParams($urlObj);
        return "https://open.weixin.qq.com/connect/oauth2/authorize?" . $bizString;
    }

    /**
     * @param $urlObj
     * @return string
     */
    public function ToUrlParams($urlObj)
    {
        $buff = "";
        foreach ($urlObj as $k => $v) {
            if ($k != "sign") {
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }

    public function postUrl($url, $postData = false, $header = false)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //返回数据不直接输出
        curl_setopt($ch, CURLOPT_POST, 1);
        //add header
        if (!empty($header)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        //add ssl support
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        //add post data support
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        $content = curl_exec($ch); //执行并存储结果
        curl_close($ch);
        return $content;
    }
}
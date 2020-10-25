<?php
namespace frontend\behaviors;

use yii\filters\auth\AuthMethod;
use yii\web\HttpException;

/**
 * Created by PhpStorm.
 * User: hu yang
 * Date: 2018/2/2
 * Time: 下午5:27
 */
class MemberAuthBehaviors extends AuthMethod
{

    /**
     * @var string the parameter name for passing the access token
     */
    const TOKEN_PARAM = 'token';



    /**
     * @param \yii\web\User     $user
     * @param \yii\web\Request  $request
     * @param \yii\web\Response $response
     *
     * @return null|\yii\web\IdentityInterface
     */
    public function authenticate($user, $request, $response)
    {
        if (!\Yii::$app->session->has('userid'))
        {
            throw new HttpException(403,"请先登录");
        }

    }

}

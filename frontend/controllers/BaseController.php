<?php
namespace frontend\controllers;
use frontend\behaviors\MemberAuthBehaviors;
use frontend\behaviors\UserAuthBehaviors;
use frontend\filter\LoginFilter;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;
use yii\web\HttpException;
use yii\web\Response;
class BaseController extends Controller
{

    public function __construct($id, $module, array $config = [])
    {
        parent::__construct($id, $module, $config);
        if (\Yii::$app->request->getIsOptions())
        {
            return "ok";
        }
        /*\Yii::$app->session->set('uid', 5);
        \Yii::$app->session->set('userid', '17788663662');
        \Yii::$app->session->set('username', '服务立项测试员1');*/
    }

    public function behaviors()
    {
        $behaviors                                 = parent::behaviors();
        $behaviors['contentNegotiator']['formats'] = ['application/json' => Response::FORMAT_JSON];
        $behaviors['loginValidate'] = [
            'class' => LoginFilter::class
        ];
        return $behaviors;
    }
}

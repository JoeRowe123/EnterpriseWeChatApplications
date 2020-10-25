<?php
namespace backend\controllers;


use common\models\AuthAssignment;
use common\models\LoginForm;
use common\models\User;
use mdm\admin\components\MenuHelper;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

include_once("../../common/components/wework/api/src/CorpAPI.class.php");
/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error', 'wework-login'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['index', 'info', 'logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $this->layout = 'left-layout';

        $menus   = MenuHelper::getAssignedMenu(Yii::$app->user->id, null, function ($menu) {
            $data = $menu['data'];

            return [
                'label' => $menu['name'],
                'url' => [$menu['route']],
                'options' => $data,
                'items' => $menu['children']
            ];
        });
        $outData = [];
        foreach ($menus as $key => $v) {
            $child = [];
            if ($v['items']) {
                foreach ($v['items'] as $k => $item) {
                    $child[$k] = [
                        'name' => $item['label'],
                        'url' => $item['url']
                    ];
                }
            }
            $outData[$key] = [
                'name' => $v['label'],
                'class' => $v['options'],
                'url' => $v['url'],
                'child' => $child
            ];
            if ($child) {
                unset($child);
            };
        }

        Yii::$app->view->params['menu'] = $outData;
        return $this->render('index');
    }


    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin($type = null)
    {
        $model = new LoginForm();
        if(!Yii::$app->user->isGuest) {
            return $this->redirect("info");
        }
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->renderPartial('login', [
                'model' => $model,
                'type' => $type,
            ]);
        }
    }

    /**
     * @return string
     */
    public function actionInfo()
    {
        return $this->render('info');
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * 企业微信登录
     * @return \yii\web\Response
     * @throws \Exception
     */
    public function actionWeworkLogin()
    {
        $code = Yii::$app->request->get('code');
        if($code) {
            $state = Yii::$app->request->get('state');
            if($state != Yii::$app->session->get("qy-state")) {
                throw new \Exception("state verification failed");
            }

            $wework = new \CorpAPI(Yii::$app->params['wework']['corpId'], Yii::$app->params['wework']['secret']);
            $user = $wework->GetUserInfoByCode($code);
            if($user) {
                $userDetail = $wework->UserGet($user->UserId);
                if($userDetail && $userDetail->userid) {
                    $userModel = User::find()->where(["userid" => $userDetail->userid])->one();
                    if(!$userModel) {
                        $userModel = User::createUser($userDetail);
                    } else {
                        $userModel->name = $userDetail->name;
                        $userModel->username = $userDetail->mobile;
                        $userModel->email = $userDetail->email;
                        $userModel->avatar = $userDetail->avatar_mediaid;
                        $userModel->gender = $userDetail->gender;
                        $userModel->mobile = $userDetail->mobile;
                        $userModel->position = $userDetail->position;
                        $userModel->department = $userDetail->department;
                        if(!$userModel->save(false)) {
                            throw new \Exception("save user info failed");
                        }
                        if(!AuthAssignment::find()->where([
                            'item_name'  => '系统超级管理员',
                            'user_id'    => $userModel->id
                        ])->exists()) {
                          User::setAuth($userModel);
                        }

                    }
                    Yii::$app->user->login($userModel,  3600 * 24 * 30);

                    return $this->goBack();
                }
            }
        }
    }



}

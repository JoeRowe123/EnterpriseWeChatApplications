<?php
namespace common\models;

use common\components\wework\InitCorp;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $name
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $userid
 * @property string $avatar
 * @property string $position
 * @property string $department
 * @property string $mobile
 * @property string $auth_key
 * @property integer $gender
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */

class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username' => '用户名',
            'name' => '姓名',
            'password' => '密码',
            'email' => '邮箱',
            'created_at' => '创建时间',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * @param $username
     * @param $password
     * @param $email
     * @throws \yii\base\Exception
     * @throws \yii\web\BadRequestHttpException
     */
    public static function createUser($userDetail)
    {
        $model                = new User();
        $model->password_hash = \Yii::$app->security->generatePasswordHash($userDetail->mobile);
        $model->generateAuthKey();
        $model->userid = $userDetail->userid;
        $model->username = $userDetail->mobile;
        $model->name = $userDetail->name;
        $model->email = $userDetail->email;
        $model->avatar = $userDetail->avatar_mediaid;
        $model->gender = $userDetail->gender;
        $model->mobile = $userDetail->mobile;
        $model->position = $userDetail->position;
        $model->department = $userDetail->department;
        $model->save();

        self::setAuth($model);

        return $model;
    }

    public static function setAuth($model)
    {
        //添加全局路由
        $authItem       = AuthItem::findOne(['name' => '/*']) ?? new AuthItem();
        $authItem->type = 2;
        $time           = time();
        if ($authItem->isNewRecord) {
            $authItem->created_at = $time;
            $authItem->name       = '/*';
        }
        $authItem->updated_at = $time;
        $authItem->save(false);

        //添加超级管理员权限
        $auth = AuthItem::findOne(['name' => '系统超级管理员']);
        if ($auth === null) {
            $auth             = new AuthItem();
            $auth->type       = 2;
            $auth->created_at = $time;
            $auth->name       = '系统超级管理员';
            $auth->updated_at = $time;
            $auth->save(false);
        }
        // 绑定超级管理员权限访问全局路由
        $authItemChild = AuthItemChild::findOne(['parent' => '系统超级管理员', 'child' => '/*']);
        if ($authItemChild === null) {
            $authItemChild = new AuthItemChild([
                'parent' => '系统超级管理员',
                'child'  => '/*'
            ]);
            $authItemChild->save(false);
        }

        // 分配当前用户到超级管理员
        $authAssignment = new AuthAssignment([
            'item_name'  => '系统超级管理员',
            'user_id'    => $model->id,
            'created_at' => $time
        ]);
        $authAssignment->save(false);
    }

    /**
     * @return array
     * @throws \Exception
     */
    public static function getAllUsers()
    {
        if(\Yii::$app->cache->exists("departmentUserInfo")) {
            $datas = json_decode(\Yii::$app->cache->get("departmentUserInfo"), true);
        } else {
            $wework = InitCorp::init();
            $datas = self::getWeWorkUsers($wework);
            \Yii::$app->cache->set("departmentUserInfo", json_encode($datas));
        }
        $res = [];
        foreach ($datas as $data) {
            self::getUsers($data, $res);
        }

        return $res;
    }

    /**
     * @param $data
     * @param $res
     */
    public static function getUsers($data, &$res)
    {
        if(isset($data['userInfo']) && $data['userInfo']) {
            foreach ($data['userInfo'] as $item) {
                $res[$item['userid']] = $item;
            }
        }

        if(isset($data['children']) && $data['children']) {
            foreach ($data['children'] as $item) {
                self::getUsers($item, $res);
            }
        }
    }
    /**
     * @param $wework
     * @return array
     * @throws \Exception
     */
    public static function getWeWorkUsers($wework)
    {
        $num = 0;
        try {
            $departmentList = $wework->DepartmentList();
            $departmentList = self::makeCategory($departmentList);

            self::sort($departmentList);

            foreach ($departmentList as &$list) {
                self::getUserInfo($wework, $list);
            }
            return $departmentList;
        } catch (\Throwable $e) {
            $num ++;
            if($num <=2 ) {
                self::getWeWorkUsers($wework);
            }
            throw new  \Exception("获取部门及成员信息失败");
        }

    }

    /**
     * @param $data
     */
    public static function sort($datas)
    {
//        $last_names = array_column($datas,'order');
//        array_multisort($last_names,SORT_DESC,$datas);
//        foreach ($datas as $data){
//            if(isset($data->children) && $data->children) {
//                self::sort($data);
//            }
//        }
    }

    /**
     * @param $wework
     * @param $data
     */
    public static function getUserInfo($wework, &$data)
    {
        $data->userInfo= $wework->UserList($data->id,0);
        if(isset($data->children) && $data->children) {
            sort($data->children);
            foreach ($data->children as $item) {
                self::getUserInfo($wework, $item);
            }
        }
    }



    public static $data = [];
    /**
     * @param $departmentList
     * @param int $pid
     * @return array
     */
    public static function makeCategory($departmentList, $pid = 0)
    {
        foreach ($departmentList as $k => $category) {
            if ($category->parentid == $pid) {
                self::$data[$category->id] = $category;
                self::makeCategory($departmentList, $category->id);
            } else {
                if(in_array($category->parentid, array_keys(self::$data))) {
                    self::$data[$category->parentid]->children[$category->id] = $category;
                }
            }
        }

//        self::dataSort(self::$data["1"]);

        return self::$data;
    }

    /**
     * @param $arrays
     */
    public static function dataSort($arrays) {
        if($arrays['children']) {
            sort($arrays['children']);
            foreach ($arrays['children'] as $array) {
                self::dataSort($array);
            }
        }
    }


}

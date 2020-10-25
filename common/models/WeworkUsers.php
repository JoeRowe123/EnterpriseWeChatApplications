<?php

namespace common\models;

use common\components\wework\InitCorp;
use Yii;

/**
 * This is the model class for table "wework_users".
 *
 * @property string $id
 * @property string $user_id 用户id（企业微信）
 * @property string $avatar 头像
 * @property string $username 用户姓名
 * @property array $department_id 部门id
 * @property string $department_name 部门名称
 * @property string $position 职位
 * @property string $phone 手机号
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class WeworkUsers extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wework_users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['department_id', 'created_at', 'updated_at'], 'required'],
            [['department_id', 'avatar', 'department_name'], 'safe'],
            [['created_at', 'updated_at'], 'integer'],
            [['user_id'], 'string', 'max' => 64],
            [['username', 'position'], 'string', 'max' => 45],
            [['phone'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户id（企业微信）',
            'username' => '用户姓名',
            'department_id' => '部门id',
            'department_name' => '部门名称',
            'position' => '职位',
            'phone' => '手机号',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * 试卷批量插入用户数据
     * @param $body
     * @param $id
     * @throws \yii\db\Exception
     */
    public static function batchInsert($body, $id)
    {
        $userArr = [];
        $oldArr = array_column(ExaminationUsers::find()->where(['paper_id' => $id])->all(), 'user_id');
        if($body['range'] == 1) {
            $nowArr = array_column($body['YuanRen'], "userid");
            $delArr = array_diff($oldArr, $nowArr);
            ExaminationUsers::deleteAll(['paper_id' => $id, "user_id" => $delArr]);
            foreach ($body['YuanRen'] as $user) {
                if(!in_array($user['userid'], $oldArr)) {
                    $departmentName = [];
                    if($model = self::find()->where(['user_id' => $user['userid']])->one()) {
                        $departmentName = $model->department_name;
                    }
                    $userArr[$user['userid']] = [
                        $id,
                        $user['userid'],
                        $user['name'],
                        $user['avatar_mediaid'],
                        $user['department'],
                        $departmentName,
                        $user['position'],
                        $user['mobile'],
                        time(),
                        time()
                    ];
                }
            }
        } else {
            foreach (WeworkUsers::find()->asArray()->all() as $user) {
                if(!in_array($user['user_id'], $oldArr)) {
                    $userArr[$user['user_id']] = [
                        $id,
                        $user['user_id'],
                        $user['username'],
                        $user['avatar'],
                        $user['department_id'],
                        json_decode($user['department_name'], true),
                        $user['position'],
                        $user['phone'],
                        time(),
                        time()
                    ];
                }
            }
        }

        \Yii::$app->db->createCommand()->batchInsert('examination_users', ['paper_id', 'user_id', 'username', 'avatar', 'department_id', 'department_name', 'position', 'phone', 'created_at', 'updated_at'], $userArr)->execute();

    }

    /**
     * 文章批量插入用户数据
     * @param $model
     * @param $id
     * @throws \yii\db\Exception
     */
    public static function batchInsertByArticle($model, $id)
    {
        $userArr = [];
        $oldArr = array_column(ArticleReadObject::find()->where(['article_id' => $id])->all(), 'user_id');
        if($model->range == 1) {
            $body = json_decode($model->read_object, true);
            $res = [];
            $nowArr = array_column($body, "userid");
            $delArr = array_diff($oldArr, $nowArr);
            ArticleReadObject::deleteAll(['article_id' => $model->id, "user_id" => $delArr]);
            foreach ($body as $k => $user) {
                if(!in_array($user['userid'], $oldArr)) {
                    if (in_array($user['userid'], $res)) {
                        unset($body[$k]);
                    } else {
                        $res[] = $user['userid'];
                    }
                    $departmentName = [];
                    if ($userModel = self::find()->where(['user_id' => $user['userid']])->one()) {
                        $departmentName = $userModel->department_name;
                    }
                    $userArr[$user['userid']] = [
                        $id,
                        $user['userid'],
                        $user['name'],
                        $user['avatar_mediaid'],
                        $user['department'],
                        $departmentName,
                        $user['position'],
                        $user['mobile'],
                        time(),
                        time()
                    ];
                }
            }
            $model->total_number = count($body);
            $model->save(false);
        } else {
            foreach (WeworkUsers::find()->asArray()->all() as $user) {
                if(!in_array($user['user_id'], $oldArr)) {
                    $userArr[$user['user_id']] = [
                        $id,
                        $user['user_id'],
                        $user['username'],
                        $user['avatar'],
                        $user['department_id'],
                        json_decode($user['department_name'], true),
                        $user['position'],
                        $user['phone'],
                        time(),
                        time()
                    ];
                }
            }

        }

        \Yii::$app->db->createCommand()->batchInsert('article_read_object', ['article_id', 'user_id', 'username', 'avatar', 'department_id', 'department_name', 'position', 'phone', 'created_at', 'updated_at'], $userArr)->execute();


    }

    /**
     * 活动批量插入用户数据
     * @param $model
     * @param $id
     * @throws \yii\db\Exception
     */
    public static function batchInsertByActivity($model, $id)
    {
        $userArr = [];
        $oldArr = array_column(ActivityReadObject::find()->where(['activity_id' => $id])->all(), 'user_id');
        if($model->range == 1) {
            $body = json_decode($model->read_object, true);
            $res = [];
            $nowArr = array_column($body, "userid");
            $delArr = array_diff($oldArr, $nowArr);
            ActivityReadObject::deleteAll(['activity_id' => $model->id, "user_id" => $delArr]);
            foreach ($body as $k => $user) {
                if(!in_array($user['userid'], $oldArr)) {
                    if (in_array($user['userid'], $res)) {
                        unset($body[$k]);
                    } else {
                        $res[] = $user['userid'];
                    }
                    $departmentName = [];
                    if ($userModel = self::find()->where(['user_id' => $user['userid']])->one()) {
                        $departmentName = $userModel->department_name;
                    }
                    $userArr[$user['userid']] = [
                        $id,
                        $user['userid'],
                        $user['name'],
                        $user['avatar_mediaid'],
                        $user['department'],
                        $departmentName,
                        $user['position'],
                        $user['mobile'],
                        time(),
                        time()
                    ];
                }
            }
            $model->total_num = count($body);
            $model->save(false);
        } else {
            foreach (WeworkUsers::find()->asArray()->all() as $user) {
                if(!in_array($user['user_id'], $oldArr)) {
                    $userArr[$user['user_id']] = [
                        $id,
                        $user['user_id'],
                        $user['username'],
                        $user['avatar'],
                        $user['department_id'],
                        json_decode($user['department_name'], true),
                        $user['position'],
                        $user['phone'],
                        time(),
                        time()
                    ];
                }
            }

        }

        \Yii::$app->db->createCommand()->batchInsert('activity_read_object', ['activity_id', 'user_id', 'username', 'avatar', 'department_id', 'department_name', 'position', 'phone', 'created_at', 'updated_at'], $userArr)->execute();


    }

    /**
     * 投票人员数据
     * @param $model
     * @param $id
     * @throws \yii\db\Exception
     */
    public static function batchInsertByVote($model, $id)
    {
        $userArr = [];
        $oldArr = array_column(VoteUserObject::find()->where(['vote_id' => $id])->all(), 'user_id');
        if($model->range == 1) {
            $body = json_decode($model->read_object, true);
            $res = [];
            $nowArr = array_column($body, "userid");
            $delArr = array_diff($oldArr, $nowArr);
            VoteUserObject::deleteAll(['vote_id' => $model->id, "user_id" => $delArr]);
            foreach ($body as $k => $user) {
                if(!in_array($user['userid'], $oldArr)) {
                    if (in_array($user['userid'], $res)) {
                        unset($body[$k]);
                    } else {
                        $res[] = $user['userid'];
                    }
                    $departmentName = [];
                    if ($userModel = self::find()->where(['user_id' => $user['userid']])->one()) {
                        $departmentName = $userModel->department_name;
                    }
                    $userArr[$user['userid']] = [
                        $id,
                        $user['userid'],
                        $user['name'],
                        $user['avatar_mediaid'],
                        $user['department'],
                        $departmentName,
                        $user['position'],
                        $user['mobile'],
                        time(),
                        time()
                    ];
                }
            }
            $model->total_num = count($body);
            $model->save(false);
        } else {
            foreach (WeworkUsers::find()->asArray()->all() as $user) {
                if(!in_array($user['user_id'], $oldArr)) {
                    $userArr[$user['user_id']] = [
                        $id,
                        $user['user_id'],
                        $user['username'],
                        $user['avatar'],
                        $user['department_id'],
                        json_decode($user['department_name'], true),
                        $user['position'],
                        $user['phone'],
                        time(),
                        time()
                    ];
                }
            }

        }

        \Yii::$app->db->createCommand()->batchInsert('vote_user_object', ['vote_id', 'user_id', 'username', 'avatar', 'department_id', 'department_name', 'position', 'phone', 'created_at', 'updated_at'], $userArr)->execute();
    }

    /**
     * 刷新企业微信成员
     * @throws \yii\db\Exception
     * @throws \Exception
     */
    public static function refreshData()
    {
        try {
            $trans = Yii::$app->db->beginTransaction();
            if(\Yii::$app->cache->exists("departmentUserInfo")) {
                \Yii::$app->cache->delete("departmentUserInfo");
            }
            $wework = InitCorp::init();
            $departmentList = User::getWeWorkUsers($wework);

            \Yii::$app->cache->set("departmentUserInfo", json_encode($departmentList));

            $users = User::getAllUsers();
            $userArr = [];
            foreach ($users as $user) {
                $departmentName = [];
                foreach ($user['department'] as $departmentId) {
                    $wework = InitCorp::init();
                    $departmentName[] = [
                        "id" => $departmentId,
                        "name" => $wework->DepartmentList($departmentId)[0]->name
                    ];
                }

                $userArr[] = [
                    $user['userid'],
                    $user['name'],
                    $user['avatar_mediaid'],
                    $user['department'],
                    $departmentName,
                    $user['position'],
                    $user['mobile'],
                    time(),
                    time()
                ];
            }
            WeworkUsers::deleteAll();
            \Yii::$app->db->createCommand()->batchInsert('wework_users', ['user_id', 'username', 'avatar', 'department_id', 'department_name', 'position', 'phone', 'created_at', 'updated_at'], $userArr)->execute();
            $trans->commit();
        } catch (\Throwable $e) {
            $trans->rollBack();
            throw new \Exception("刷新企业微信成员信息失败，原因：".$e->getMessage());
        }

    }
}

<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "activity_read_object".
 *
 * @property string $id
 * @property int $activity_id 文章id
 * @property string $user_id 用户id
 * @property string $username 用户姓名
 * @property int $department_id 部门id
 * @property string $department_name 部门名称
 * @property string $avatar 头像
 * @property string $position 职位
 * @property string $phone 手机号
 * @property int $read_time 阅读时间
 * @property int $is_read 是否阅读;1=>是;0=>否;
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 * @property int $is_receive_msg 是否收到提醒消息
 * @property int $apply_status 报名状态；0=》未报名；1=》已报名；-1=》已取消；
 * @property int $apply_at 报名时间；
 * @property string $apply_options 报名表单提交值；
 */
class ActivityReadObject extends \yii\db\ActiveRecord
{

    public static $applyStatus = [
        0 => '未报名',
        1 => '已报名',
        -1 => '已取消',
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'activity_read_object';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['activity_id', 'read_time', 'is_read', 'created_at', 'updated_at', 'is_receive_msg', 'apply_at', 'apply_status'], 'integer'],
            [['created_at', 'updated_at', 'user_id'], 'required'],
            [['username', 'position'], 'string', 'max' => 45],
            [['phone'], 'string', 'max' => 20],
            [['apply_options', 'avatar', 'department_name'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'activity_id' => '活动id',
            'user_id' => '用户id',
            'username' => '用户姓名',
            'department_id' => '部门id',
            'department_name' => '部门名称',
            'position' => '职位',
            'phone' => '手机号',
            'read_time' => '阅读时间',
            'is_read' => '是否阅读;1=>是;0=>否;',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'apply_status' => '报名状态',
            'apply_at' => '报名时间',
            'apply_options' => '报名表单提交值',
        ];
    }

    public function attributes ()
    {
        $attributes = parent::attributes();
        $attributes[] = 'depart';
        return $attributes;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasMany(ActivityReadObject::class, ["department_id" => "department_id"])->select(["id","username","department_id","department_name"]);
    }

    public static function batchInsert($datas)
    {

    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActivity()
    {
        return $this->hasOne(Activity::class, ["id" => "activity_id"]);
    }
}

<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "vote_user_object".
 *
 * @property string $id
 * @property int $vote_id 投票id
 * @property string $user_id 用户id（企业微信）
 * @property string $username 用户姓名
 * @property array $department_id 部门id
 * @property string $department_name 部门名称
 * @property string $position 职位
 * @property string $avatar 头像
 * @property string $phone 手机号
 * @property int $is_receive_msg 是否收到提醒消息
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class VoteUserObject extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vote_user_object';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['vote_id', 'is_receive_msg', 'created_at', 'updated_at'], 'integer'],
            [['department_id', 'created_at', 'updated_at'], 'required'],
            [['department_id', 'avatar', 'department_name'], 'safe'],
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
            'vote_id' => '投票id',
            'user_id' => '用户id（企业微信）',
            'username' => '用户姓名',
            'department_id' => '部门id',
            'department_name' => '部门名称',
            'position' => '职位',
            'phone' => '手机号',
            'is_receive_msg' => '是否收到提醒消息',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}

<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "examination_users".
 *
 * @property string $id
 * @property int $paper_id 试卷id
 * @property string $user_id 用户id（企业微信）
 * @property string $username 用户姓名
 * @property array $department_id 部门id
 * @property string $department_name 部门名称
 * @property string $position 职位
 * @property string $avatar 头像
 * @property string $phone 手机号
 * @property int $start_at 开始考试时间
 * @property int $end_at 结束考试时间
 * @property int $total_time 用时（分）
 * @property int $grade 分数
 * @property int $status 状态；0=》未通过；1=》已通过；2=》阅卷中；
 * @property int $is_join 是否参与;1=>是;0=>否;
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 * @property int $is_receive_msg 是否收到提醒消息
 */
class ExaminationUsers extends \yii\db\ActiveRecord
{
    public static $status = [
        0 => '未通过',
        1 => '已通过',
        2 => '阅卷中',
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'examination_users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['paper_id', 'created_at', 'updated_at', 'start_at', 'end_at', 'total_time', 'grade', 'status', 'is_join', 'is_receive_msg'], 'integer'],
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
            'paper_id' => '试卷id',
            'user_id' => '用户id（企业微信）',
            'username' => '用户姓名',
            'department_id' => '部门id',
            'department_name' => '部门名称',
            'position' => '职位',
            'phone' => '手机号',
            'end_at' => '结束考试时间',
            'start_at' => '开始考试时间',
            'total_time' => '用时（分）',
            'grade' => '分数',
            'status' => '状态',
            'is_join' => '是否参与',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaperInfo()
    {
        return $this->hasOne(ExaminationPaper::class, ["id" => "paper_id"]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaperItems()
    {
        return $this->hasMany(ExaminationPaperItem::class, ["paper_id" => "paper_id"]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaperAnswers()
    {
        return $this->hasMany(ExaminationAnswer::class, ["examination_user_id" => "id"]);
    }
}

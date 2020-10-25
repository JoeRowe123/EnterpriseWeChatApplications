<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "vote".
 *
 * @property string $id
 * @property string $title 标题
 * @property array $image 封面图
 * @property int $type 类型;1=>图文投票;2=>文字投票;
 * @property array $options 选项
 * @property int $start_time 开始时间
 * @property int $end_time 结束时间
 * @property int $option_type 投票选项；1=>单选;2=>多选;
 * @property int $multiple_num 多选个数
 * @property int $vote_type 投票类型；1=>实名投票;2=>匿名投票;
 * @property int $is_repetition 是否允许重复投票;0=>否;1=>是;
 * @property int $is_view 投票之后立即查看结果;0=>否;1=>是;
 * @property int $is_notice 是否推送消息;0=>否;1=>是;
 * @property int $status 状态;20=>未开始;10=>进行中;30=>已结束;
 * @property int $author_id 创建人
 * @property string $author_name 创建人
 * @property int $total_num 总人数
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 * @property string $read_object 范围对象
 * @property string $user_department_object 范围对象部门
 * @property int $range 范围对象类型
 * @property int $is_ten 是否推送消息（10分钟）
 */
class Vote extends \yii\db\ActiveRecord
{
    const STATUS_DRAFT = 0;
    const STATUS_NOT_START = 20;
    const STATUS_GOING = 10;
    const STATUS_END = 30;

    public static $status = [
        self::STATUS_DRAFT => '草稿',
        self::STATUS_NOT_START => '未开始',
        self::STATUS_GOING => '进行中',
        self::STATUS_END => '已结束',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vote';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'options', 'start_time', 'end_time', 'created_at', 'updated_at'], 'required'],
            [['image', 'options', 'read_object', 'user_department_object'], 'safe'],
            [['type', 'option_type', 'multiple_num', 'vote_type', 'is_repetition', 'is_view', 'is_notice', 'status', 'author_id', 'created_at', 'updated_at', 'total_num', 'range', 'is_ten'], 'integer'],
            [['title'], 'string', 'max' => 64],
            [['multiple_num'], 'integer', 'min' => 1],
            [['author_name'], 'string', 'max' => 32],
            [['start_time'], 'definedRule', 'skipOnEmpty' => false, 'skipOnError' => false],
            [['end_time'], 'definedRule', 'skipOnEmpty' => false, 'skipOnError' => false],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '标题',
            'image' => '封面图',
            'type' => '类型',
            'options' => '选项',
            'start_time' => '开始时间',
            'end_time' => '结束时间',
            'option_type' => '投票选项',
            'multiple_num' => '多选个数',
            'vote_type' => '投票方式',
            'is_repetition' => '是否允许重复投票',
            'is_view' => '投票之后立即查看结果',
            'is_notice' => '是否推送消息',
            'status' => '状态',
            'author_id' => '创建人',
            'author_name' => '创建人',
            'total_num' => '总人数',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVoteOptions()
    {
        return $this->hasMany(VoteOption::class, ['vote_id' => 'id'])->orderBy("num desc,id asc");
    }


    public function getOptionsTotal()
    {
        return $this->hasMany(VoteOption::class, ['vote_id' => 'id'])->select(["vote_id","sum(num) as total"]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVoteRecords()
    {
        return $this->hasMany(VoteRecord::class, ['vote_id' => 'id'])->where(['user_id'=>Yii::$app->session->get('uid')]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVoteRecordsList()
    {
        return $this->hasMany(VoteRecord::class, ['vote_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(VoteUserObject::class, ['vote_id' => 'id']);
    }

    /**
     * @return $this
     */
    public function getIsVote()
    {
        return $this->hasOne(VoteRecord::class, ["vote_id" => "id"])->where(["user_id" => Yii::$app->session->get('uid')]);
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function definedRule($attribute, $params)
    {
        if ($this->start_time && $this->end_time)
        {
            if ($this->start_time > $this->end_time)
                $this->addError($attribute, "开始时间不能大于结束时间。");
            return;
        }
    }
}

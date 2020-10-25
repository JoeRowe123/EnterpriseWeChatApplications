<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "activity".
 *
 * @property string $id
 * @property string $title 活动标题
 * @property array $image 封面图
 * @property string $address 活动地点
 * @property int $start_time 活动开始时间
 * @property int $end_time 活动结束时间
 * @property int $close_date 报名截止时间
 * @property string $content 正文内容
 * @property string $theme 活动主题
 * @property array $attachment 附件
 * @property int $limit_person_num 报名人数限制
 * @property int $is_push_msg 是否推送消息;0=>否;1=>是;
 * @property int $status 状态;0=>草稿;10=>未开始;20=>进行中;30=>已结束;
 * @property int $apply_num 报名人数
 * @property int $view_num 阅读人数
 * @property int $comment_num 评论数
 * @property int $like_num 点赞数
 * @property int $total_num 总人数
 * @property int $author_id 发起人id
 * @property string $author_name 发起人
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 * @property string $read_object 阅读对象
 * @property string $user_department_object 阅读对象部门
 * @property int $range 阅读对象类型
 * @property int $is_ten 是否推送消息（10分钟）
 * @property int $is_thirty 是否推送消息（30分钟）
 */
class Activity extends \yii\db\ActiveRecord
{
    const STATUS_NOT_START = 10;
    const STATUS_GOING = 20;
    const STATUS_END = 30;
    const STATUS_DRAFT = 0;

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
        return 'activity';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at', 'title', 'start_time', 'end_time', 'close_date', 'address', 'theme'], 'required'],
            [['image', 'attachment', 'read_object', 'user_department_object'], 'safe'],
            [['limit_person_num', 'is_push_msg', 'status', 'apply_num', 'view_num', 'comment_num', 'like_num', 'total_num', 'author_id', 'created_at', 'updated_at', 'range', 'is_ten', 'is_thirty'], 'integer'],
            [['content'], 'string'],
            [['title', 'theme'], 'string', 'max' => 64],
            [['address'], 'string', 'max' => 64],
            [['author_name'], 'string', 'max' => 32],
            [['close_date'], 'definedRuleByCloseDate', 'skipOnEmpty' => false, 'skipOnError' => false],
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
            'title' => '活动标题',
            'theme' => '活动主题',
            'image' => '封面图',
            'address' => '活动地点',
            'start_time' => '活动开始时间',
            'end_time' => '活动结束时间',
            'close_date' => '报名截止时间',
            'content' => '正文内容',
            'attachment' => '附件',
            'limit_person_num' => '报名人数限制',
            'is_push_msg' => '是否推送消息',
            'status' => '状态',
            'apply_num' => '报名人数',
            'view_num' => '阅读人数',
            'comment_num' => '评论数量',
            'like_num' => '点赞数量',
            'total_num' => '总人数',
            'author_id' => '发起人id',
            'author_name' => '发起人',
            'created_at' => '发布时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItems()
    {
        return $this->hasMany(ActivityItem::class, ["activity_id" => "id"]);
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

    /**
     * @param $attribute
     * @param $params
     */
    public function definedRuleByCloseDate($attribute, $params)
    {
        if ($this->start_time && $this->close_date)
        {
            if ($this->close_date > $this->start_time)
                $this->addError($attribute, "报名截止时间不能大于开始时间。");
            return;
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComment()
    {
        return $this->hasMany(ActivityComment::class, ['activity_id' => "id"])->select(['activity_comment.*',"u.avatar"])->where(["pid"=>0])->innerJoin("user u","u.id = activity_comment.user_id")->orderBy("created_at desc");
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvite()
    {
        return $this->hasMany(ActivityReadObject::class, ['activity_id' => "id"]);
    }

    public function getLike()
    {
        return $this->hasOne(ActivityLike::class,["activity_id"=>"id"])->where(['user_id' => Yii::$app->session->get('uid')]);
    }

    /**
     * @return $this
     */
    public function getApply()
    {
        return $this->hasOne(ActivityReadObject::class, ['activity_id' => "id"])->where(['user_id' => Yii::$app->session->get('userid'),"apply_status"=>1]);
    }
}

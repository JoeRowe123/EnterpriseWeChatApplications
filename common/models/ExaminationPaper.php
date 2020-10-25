<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "examination_paper".
 *
 * @property int $id
 * @property string $sn 试卷编号
 * @property string $name 试卷名称
 * @property int $type 选题模式;1=>自主选题;2=>固定规则抽题;3=>随机生成题目;
 * @property array $question_bank_ids 题库ids
 * @property array $questions_rule 抽题规则
 * @property int $start_time 考试开始时间
 * @property int $end_time 考试结束时间
 * @property int $duration_time 考试时长（分钟）
 * @property int $is_notice 是否发送考试通知提醒;0=>否;1=>是;
 * @property int $is_remind 是否开启考试提醒;0=>否;1=>是;
 * @property int $pass_mark 及格分数
 * @property string $explain 考试说明
 * @property int $status 状态;0=>草稿;10=>未开始;20=>进行中;30=>已结束;
 * @property int $total_grade 总分
 * @property int $participant_num 参与人员人数
 * @property int $not_participant_num 未参与人员人数
 * @property int $total_num 总人数
 * @property int $range 考试范围类型
 * @property string $user_object 选择人员对象
 * @property string $user_department_object 选择的部门
 * @property int $author_id 创建人id
 * @property string $author_name 创建人
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 * @property int $is_all 是否选择固定规则所有题目
 * @property int $total_items 总题数
 * @property int $is_ten 是否推送消息（10分钟）
 * @property int $is_thirty 是否推送消息（30分钟）
 * @property int $is_an_hour 是否推送消息（1小时）
 */
class ExaminationPaper extends \yii\db\ActiveRecord
{

    const STATUS_NOT_START = 10;
    const STATUS_GOING = 20;
    const STATUS_END = 30;
    const STATUS_DRAFT  = 0;

    public static $status = [
        self::STATUS_DRAFT => '草稿',
        self::STATUS_NOT_START => '未开始',
        self::STATUS_GOING => '进行中',
        self::STATUS_END => '已结束',
    ];

    public static $type = [
        1 => '自主选题',
        2 => '固定规则抽题',
        3 => '随机生成题目'
    ];


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'examination_paper';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'start_time', 'end_time', 'duration_time', 'is_notice', 'is_remind', 'pass_mark', 'status', 'total_grade', 'participant_num', 'not_participant_num', 'total_num', 'author_id', 'created_at', 'updated_at', 'range', 'is_all', 'total_items', 'is_ten', 'is_thirty', 'is_an_hour'], 'integer'],
            [['question_bank_ids', 'start_time', 'end_time', 'created_at'], 'required'],
            [['question_bank_ids', 'questions_rule', 'user_object', 'user_department_object'], 'safe'],
            [['sn', 'author_name'], 'string', 'max' => 32],
            [['name'], 'string', 'max' => 64],
            [['explain'], 'string', 'max' => 500],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sn' => '试卷编号',
            'name' => '试卷名称',
            'type' => '选题模式',
            'question_bank_ids' => '题库ids',
            'questions_rule' => '抽题规则',
            'start_time' => '考试开始时间',
            'end_time' => '考试结束时间',
            'duration_time' => '考试时长',
            'is_notice' => '是否发送考试通知提醒;0=>否;1=>是;',
            'is_remind' => '是否开启考试提醒;0=>否;1=>是;',
            'pass_mark' => '及格分数',
            'explain' => '考试说明',
            'status' => '状态',
            'total_grade' => '总分',
            'participant_num' => '参与人员人数',
            'not_participant_num' => '未参与人员人数',
            'total_num' => '总人数',
            'author_id' => '创建人id',
            'author_name' => '创建人',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'is_all' => '是否选择固定规则所有题目',
            'total_items' => '总题数',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(ExaminationUsers::class, ["paper_id"=> "id"]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItems()
    {
        return $this->hasMany(ExaminationPaperItem::class, ["paper_id"=> "id"]);
    }

    public function getExamInfo()
    {
        return $this->hasOne(ExaminationUsers::class,['paper_id'=>'id'])->where(['user_id'=>Yii::$app->session->get('userid')]);
    }

    /*public function getReading()
    {
        return $this->hasOne(ExaminationAnswer::class,['paper_id'=>'id'])->where(['is_true'=>-1])->where(['user_id'=>Yii::$app->session->get('userid')]);
    }

    public function getPaperGrade()
    {
        return $this->hasMany(ExaminationAnswer::class,['paper_id'=>'id'])->select("*,sum(grade) as total_score")->where(['user_id'=>Yii::$app->session->get('userid')]);
    }

    public function getDone()
    {
        return $this->hasOne(ExaminationAnswer::class,['paper_id'=>"id"])->where(['user_id'=>Yii::$app->session->get('userid')]);
    }*/
}

<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "question_bank_item".
 *
 * @property string $id
 * @property int $bank_id 题库id
 * @property int $type 题库类型;1=>单选题;2=>多选题;3=>判断题;4=>填空题;
 * @property int $sort 排序
 * @property int $grade 分数
 * @property string $title 题目名称
 * @property array $options 选项
 * @property array $answer 正确答案
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 * @property int $status 状态
 */
class QuestionBankItem extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 10;
    const STATUS_DISABLED  = 0;

    public static $status = [
        self::STATUS_ACTIVE => '启用',
        self::STATUS_DISABLED => '禁用',
    ];
    
    const TYPE_SINGLE      = 1;
    const TYPE_MULTIPLE    = 2;
    const TYPE_JUDGE       = 3;
    const TYPE_GAP_FILLING = 4;

    public static $type = [
        self::TYPE_SINGLE => '单选题',
        self::TYPE_MULTIPLE => '多选题',
        self::TYPE_JUDGE => '判断题',
        self::TYPE_GAP_FILLING => '填空题',
    ];

    public static $typeEnum = [
        '单选题' => self::TYPE_SINGLE,
        '多选题' => self::TYPE_MULTIPLE,
        '判断题' => self::TYPE_JUDGE ,
        '填空题' =>self::TYPE_GAP_FILLING,
    ];

    public $answer2;
    public $answer3;
    public $answer4;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'question_bank_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['answer'], 'requiredByASpecial', 'skipOnEmpty' => false, 'skipOnError' => false],
            [['answer2'], 'requiredByASpecial2', 'skipOnEmpty' => false, 'skipOnError' => false],
            [['answer3'], 'requiredByASpecial3', 'skipOnEmpty' => false, 'skipOnError' => false],
            [['answer4'], 'requiredByASpecial4', 'skipOnEmpty' => false, 'skipOnError' => false],
            [['bank_id', 'type', 'sort', 'grade', 'title'], 'required'],
            [['bank_id', 'type', 'sort', 'grade', 'created_at', 'updated_at', 'status'], 'integer'],
            [['options'], 'safe'],
            [['title'], 'string', 'max' => 500],
            [['sort', 'grade'], 'integer', 'min' => 0],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'bank_id' => '题库id',
            'type' => '题型',
            'sort' => '排序',
            'grade' => '分数',
            'title' => '题目名称',
            'options' => '选项',
            'answer' => '正确答案',
            'answer2' => '正确答案',
            'answer3' => '正确答案',
            'answer4' => '正确答案',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'status' => '状态',
        ];
    }


    /**
     * @param $attribute
     * @param $params
     */
    public function requiredByASpecial($attribute, $params)
    {
        if ($this->type == self::TYPE_GAP_FILLING)
        {
            if (!$this->answer)
                $this->addError($attribute, "正确答案不能为空。");
                return;
        }
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function requiredByASpecial2($attribute, $params)
    {
        if ($this->type == self::TYPE_JUDGE)
        {
            if (!$this->answer2)
                $this->addError($attribute, "正确答案不能为空。");
                return;
        }
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function requiredByASpecial3($attribute, $params)
    {
        if ($this->type == self::TYPE_MULTIPLE)
        {
            if (!$this->answer3)
                $this->addError($attribute, "正确答案不能为空。");
                return;
        }
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function requiredByASpecial4($attribute, $params)
    {
        if ($this->type == self::TYPE_SINGLE)
        {
            if (!$this->answer4)
                $this->addError($attribute, "正确答案不能为空。");
                return;
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestionBank()
    {
        return $this->hasOne(QuestionBank::class, ['id' => 'bank_id']);
    }
}

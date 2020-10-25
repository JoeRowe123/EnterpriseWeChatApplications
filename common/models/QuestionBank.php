<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "question_bank".
 *
 * @property string $id
 * @property string $sn 编号
 * @property string $name 名称
 * @property int $single_num 单选题数量
 * @property int $multiple_num 多选题数量
 * @property int $judge_num 判断题数量
 * @property int $gap_filling_num 填空题数量
 * @property int $total_num 题目总数量
 * @property int $author_id 创建人id
 * @property string $author_name 创建人姓名
 * @property int $status 状态;0=>禁用;10=>禁用;
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class QuestionBank extends \yii\db\ActiveRecord
{
    public $batch_import;

    const STATUS_ACTIVE = 10;
    const STATUS_DISABLED  = 0;

    public static $status = [
        self::STATUS_ACTIVE => '启用',
        self::STATUS_DISABLED => '禁用',
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'question_bank';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['batch_import'], 'safe'],
            [['single_num', 'multiple_num', 'judge_num', 'gap_filling_num', 'total_num', 'author_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['sn', 'author_name'], 'string', 'max' => 32],
            [['name'], 'string', 'max' => 64],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sn' => '编号',
            'name' => '题库名称',
            'single_num' => '单选题数量',
            'multiple_num' => '多选题数量',
            'judge_num' => '判断题数量',
            'gap_filling_num' => '填空题数量',
            'total_num' => '题目总数量',
            'author_id' => '创建人id',
            'author_name' => '创建人姓名',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'batch_import' => '试题模板',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItems()
    {
        return $this->hasMany(QuestionBankItem::class, ['bank_id' => 'id']);
    }
}

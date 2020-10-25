<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "examination_paper_item".
 *
 * @property string $id
 * @property int $paper_id 试卷id
 * @property int $bank_id 题库id
 * @property string $bank_name 题库名称
 * @property int $item_id 试题id
 * @property string $item_title 题目名称
 * @property string $item_option 题目选项
 * @property string $item_answer 题目答案
 * @property int $item_type 题型;1=>单选题;2=>多选题;3=>判断题;4=>填空题;
 * @property int $item_grade 分数
 * @property int $sort 排序
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class ExaminationPaperItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'examination_paper_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['paper_id', 'bank_id', 'item_id', 'item_type', 'item_grade', 'sort', 'created_at', 'updated_at'], 'integer'],
            [['created_at', 'updated_at'], 'required'],
            [['bank_name'], 'string', 'max' => 64],
            [['item_title'], 'string', 'max' => 500],
            [['item_option', 'item_answer'], 'save'],
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
            'bank_id' => '题库id',
            'bank_name' => '题库名称',
            'item_id' => '试题id',
            'item_title' => '题目名称',
            'item_option' => '题目选项',
            'item_answer' => '题目答案',
            'item_type' => '题型;1=>单选题;2=>多选题;3=>判断题;4=>填空题;',
            'item_grade' => '分数',
            'sort' => '排序',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaperAnswer()
    {
        return $this->hasOne(ExaminationAnswer::class, ["examination_item_id" => "id"]);
    }

    public function getReference()
    {
        return $this->hasOne(QuestionBankItem::class, ["id" => "item_id"]);
    }

    public function getMyAnswer()
    {
        return $this->hasOne(ExaminationAnswer::class, ["examination_item_id" => "id"])->where(["user_id"=>Yii::$app->session->get("userid")]);
    }

    public function getExamInfo()
    {
        return $this->hasOne(ExaminationUsers::class,["paper_id"=>"paper_id"])->where(["user_id"=>Yii::$app->session->get("userid")]);
    }
}

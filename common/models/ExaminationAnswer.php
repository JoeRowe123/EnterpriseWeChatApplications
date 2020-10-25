<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "examination_answer".
 *
 * @property string $id
 * @property int $examination_user_id 试卷关联用户id
 * @property string $user_id 企业微信id
 * @property int $paper_id 试卷id
 * @property int $examination_item_id 试卷题目id
 * @property array $answer 答案
 * @property int $is_true 是否回答正确;0=>错误;1=>正确;
 * @property int $grade 得分
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class ExaminationAnswer extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'examination_answer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['examination_user_id', 'paper_id', 'examination_item_id', 'is_true', 'created_at', 'updated_at', 'grade'], 'integer'],
            [['answer', 'created_at', 'updated_at'], 'required'],
            [['answer'], 'safe'],
            [['user_id'], 'string', 'max' => 64],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'examination_user_id' => '试卷关联用户id',
            'user_id' => '企业微信id',
            'paper_id' => '试卷id',
            'examination_item_id' => '试卷题目id',
            'answer' => '答案',
            'is_true' => '是否回答正确;0=>错误;1=>正确;-1=>待评阅',
            'grade' => '得分',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaper()
    {
        return $this->hasOne(ExaminationPaper::class, ["id" => "paper_id"]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaperItem()
    {
        return $this->hasOne(ExaminationPaperItem::class, ["id" => "examination_item_id"]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExaminationUser()
    {
        return $this->hasOne(ExaminationUsers::class, ["id" => "examination_user_id"]);
    }
}

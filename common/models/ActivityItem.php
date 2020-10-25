<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "activity_item".
 *
 * @property string $id
 * @property int $activity_id 活动id
 * @property int $item_type 题目类型;1=>单选;2=>多选;3=>问答;
 * @property string $item_title 题目标题
 * @property array $item_options 题目选项
 * @property int $is_must 是否必答;1=>是;-1=否;
 */
class ActivityItem extends \yii\db\ActiveRecord
{
    public static $type = [
        1 => '单选题',
        2 => '多选题',
        3 => '问答题'
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'activity_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['activity_id', 'item_type', 'is_must'], 'integer'],
            [['item_title', 'item_type', 'is_must'], 'required'],
            [['item_options'], 'safe'],
            [['item_title'], 'string', 'max' => 200],
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
            'item_type' => '题目类型',
            'item_title' => '题目标题',
            'item_options' => '题目选项',
            'is_must' => '是否必答',
        ];
    }
}

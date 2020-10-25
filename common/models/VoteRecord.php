<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "vote_record".
 *
 * @property string $id
 * @property int $vote_id 投票id
 * @property int $user_id 用户id
 * @property string $username 用户姓名
 * @property array $option_ids 选项id
 * @property string $desc 选项信息
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class VoteRecord extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vote_record';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['vote_id', 'user_id', 'created_at', 'updated_at'], 'integer'],
            [['option_ids', 'desc', 'created_at', 'updated_at'], 'required'],
            [['option_ids'], 'safe'],
            [['desc'], 'safe'],
            [['username'], 'string', 'max' => 32],
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
            'user_id' => '用户id',
            'username' => '用户姓名',
            'option_ids' => '选项id',
            'desc' => '选项信息',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}

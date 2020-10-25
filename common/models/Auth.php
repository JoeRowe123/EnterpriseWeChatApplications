<?php

namespace common\models;

use common\components\active\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "auth".
 *
 * @property int $source_type 来源类型
 * @property string $open_id
 * @property int $created_at
 * @property int $updated_at
 * @property int $member_id 用户id
 *
 * @property Member $member
 */
class Auth extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'auth';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['source_type', 'open_id'], 'required'],
            [['source_type', 'created_at', 'updated_at', 'member_id'], 'integer'],
            [['open_id'], 'string', 'max' => 120],
            [['member_id'], 'exist', 'skipOnError' => true, 'targetClass' => Member::className(), 'targetAttribute' => ['member_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'source_type' => '来源类型',
            'open_id' => 'Open ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'member_id' => '用户id',
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(Member::className(), ['id' => 'member_id']);
    }
}

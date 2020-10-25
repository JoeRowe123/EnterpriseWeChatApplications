<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "vote_option".
 *
 * @property string $id
 * @property int $vote_id 投票id
 * @property int $num 投票数
 * @property string $option_name 投票选项
 * @property string $option_image 选项图片
 */
class VoteOption extends \yii\db\ActiveRecord
{
    public $type;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vote_option';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['option_image'], 'definedRule', 'skipOnEmpty' => false, 'skipOnError' => false],
            ['option_name', 'required'],
            [['vote_id'], 'integer'],
            [['option_name'], 'string', 'max' => 255],
            ['type', 'integer'],
            ['num', 'integer'],
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
            'option_name' => '投票选项',
            'option_image' => '选项图片',
            'num' => '票数',
        ];
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function definedRule($attribute, $params)
    {
        if ($this->type == 1 && !$this->option_image)
        {
            $this->addError($attribute, "选项图片不能为空。");
            return;
        }
    }

}

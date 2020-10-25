<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "article_like".
 *
 * @property string $id
 * @property int $user_id 用户id
 * @property int $article_id 文章id
 * @property string $username 用户名称
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class ArticleLike extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'article_like';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'article_id', 'created_at', 'updated_at'], 'integer'],
            [['created_at', 'updated_at'], 'required'],
            [['username'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户id',
            'article_id' => '文章id',
            'username' => '用户名称',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}

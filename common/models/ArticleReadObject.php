<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "article_read_object".
 *
 * @property string $id
 * @property int $article_id 文章id
 * @property string $user_id 用户id
 * @property string $username 用户姓名
 * @property string $department_id 部门id
 * @property string $department_name 部门名称
 * @property string $avatar 头像
 * @property string $position 职位
 * @property string $phone 手机号
 * @property int $read_time 阅读时间
 * @property int $is_read 是否阅读;1=>是;0=>否;
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 * @property int $is_receive_msg 是否收到提醒消息
 */
class ArticleReadObject extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'article_read_object';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['article_id', 'read_time', 'is_read', 'created_at', 'updated_at', 'is_receive_msg'], 'integer'],
            [['read_time', 'created_at', 'updated_at', 'user_id'], 'required'],
            [['username', 'position'], 'string', 'max' => 45],
            [['phone'], 'string', 'max' => 20],
            [['avatar', 'department_name', 'department_id'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'article_id' => '文章id',
            'user_id' => '用户id',
            'username' => '用户姓名',
            'department_id' => '部门id',
            'department_name' => '部门名称',
            'position' => '职位',
            'phone' => '手机号',
            'read_time' => '阅读时间',
            'is_read' => '是否阅读;1=>是;0=>否;',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArticle()
    {
        return $this->hasOne(Article::class, ["id" => "article_id"]);
    }
}

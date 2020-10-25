<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "activity_comment".
 *
 * @property string $id
 * @property int $user_id 用户id
 * @property int $activity_id 文章id
 * @property int $del 是否删除
 * @property string $username 用户名称
 * @property string $content 评论内容
 * @property int $created_at 评论时间
 * @property int $updated_at 更新时间
 * @property int $pid
 * @property int $rid
 */
class ActivityComment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'activity_comment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'activity_id', 'created_at', 'updated_at','pid', 'del'], 'integer'],
            [['content'], 'string'],
            [['created_at', 'updated_at'], 'required'],
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
            'user_id' => '用户id',
            'article_id' => '文章id',
            'username' => '评论人',
            'content' => '评论内容',
            'created_at' => '评论时间',
            'updated_at' => '更新时间',
        ];
    }

    public function getChildren()
    {
        return $this->hasMany(ActivityComment::class, ["pid" => "id"])->select(['activity_comment.*',"u.avatar"])->innerJoin("user u","u.id = activity_comment.user_id")->orderBy("created_at desc");
    }

    public function getLike()
    {
        return $this->hasOne(CommentLike::class, ["comment_id" => "id"])->where(["type" => CommentLike::ACTIVITY, "user_id" => Yii::$app->session->get('uid')]);
    }

    public function getQuest()
    {
        return $this->hasOne(ArticleComment::class, ["id" => "rid"]);
    }
}

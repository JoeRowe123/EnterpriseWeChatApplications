<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "article".
 *
 * @property string $id
 * @property string $title 标题
 * @property array $image 封面图
 * @property string $abstract 摘要
 * @property int $reading_time 阅读期限
 * @property int $is_push_msg 是否推送消息；1=>是;0=>否;
 * @property int $is_important_msg 是否为重要消息；1=>是;0=>否;
 * @property string $content 正文
 * @property int $status 状态;0=>草稿;10=>已发布;
 * @property int $author_id 作者id
 * @property string $author_name 作者姓名
 * @property int $first_category_id 顶级分类id
 * @property int $third_category_id 三级分类id
 * @property int $second_category_id 二级分类id
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 * @property int $type 文章类型；1=>天一酷讯;2=>微学堂;
 * @property int $view_number 阅读人数
 * @property int $comment_number 评论人数
 * @property int $like_number 点赞人数
 * @property int $total_number 总人数
 * @property int $is_secrecy 消息保密
 * @property string $read_object 阅读对象
 * @property string $user_department_object 阅读对象部门
 * @property int $range 阅读对象类型
 * @property string $attachment 附件
 * @property int $timing_date 定时发布时间
 */
class Article extends \yii\db\ActiveRecord
{
    const TYPE_TYKX = 1;
    const TYPE_WXT  = 2;

    const STATUS_ACTIVE = 10;
    const STATUS_WAIT = 20;
    const STATUS_DRAFT  = 0;

    public static $status = [
        self::STATUS_ACTIVE => '已发布',
        self::STATUS_WAIT => '待发布',
        self::STATUS_DRAFT => '草稿'
    ];

    public static $type = [
        self::TYPE_TYKX => '天一酷讯',
        self::TYPE_WXT => '微学堂'
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'article';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'first_category_id', 'is_push_msg', 'is_important_msg'], 'required'],
            [['image', 'reading_time', 'range'], 'safe'],
            [['is_push_msg', 'is_important_msg', 'status', 'author_id', 'third_category_id', 'created_at', 'updated_at', 'type', 'view_number', 'comment_number', 'like_number', 'total_number', 'is_secrecy', 'second_category_id', 'first_category_id'], 'integer'],
            [['content'], 'string'],
            [['title'], 'string', 'max' => 64],
            [['abstract'], 'string', 'max' => 210],
            [['author_name'], 'string', 'max' => 32],
            [['read_object', 'attachment', 'user_department_object'], 'safe'],
            [['timing_date'], 'definedRule', 'skipOnEmpty' => false, 'skipOnError' => false],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '标题',
            'image' => '封面图',
            'abstract' => '摘要',
            'reading_time' => '阅读期限',
            'is_push_msg' => '是否推送消息',
            'is_important_msg' => '是否为重要消息',
            'content' => '正文内容',
            'status' => '状态',
            'author_id' => '作者id',
            'author_name' => '作者姓名',
            'first_category_id' => '一级分类',
            'second_category_id' => '二级分类',
            'third_category_id' => '三级分类',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'type' => '文章类型',
            'view_number' => '阅读人数',
            'comment_number' => '评论数量',
            'like_number' => '点赞数量',
            'total_number' => '总人数',
            'read_object' => '阅读对象',
            'is_secrecy' => '消息保密',
            'attachment' => '附件',
            'timing_date' => '定时发布时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(ArticleComment::class, ["article_id"=> "id"])->select(['article_comment.*',"u.avatar"])->where(["pid"=>0])->innerJoin("user u","u.id = article_comment.user_id")->orderBy("created_at desc");
    }

    public function getFirst()
    {
        return $this->hasOne(ArticleCategory::class,['id'=>'first_category_id'])->select('id,name');
    }

    public function getSecond()
    {
        return $this->hasOne(ArticleCategory::class,['id'=>'second_category_id'])->select('id,name');
    }

    public function getThird()
    {
        return $this->hasOne(ArticleCategory::class,['id'=>'third_category_id'])->select('id,name');
    }

    public function getLike()
    {
        return $this->hasOne(ArticleLike::class, ["article_id" => "id"])->where(['user_id'=>Yii::$app->session->get('uid')]);
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function definedRule($attribute, $params)
    {
        if ($this->status == 20)
        {
            if (!$this->timing_date)
                $this->addError($attribute, "定时发布任务不能为空。");
            return;
        }
    }
}

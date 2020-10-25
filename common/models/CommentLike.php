<?php
/**
 * Copyright(c) 2018-2050,BWSC.Co.Ltd.
 * Created by PhpStorm.
 * User: JoeRowe
 * Date: 2019/11/12 0012
 * Time: 17:48
 *
 */

namespace common\models;


use yii\db\ActiveRecord;

class CommentLike extends ActiveRecord
{
    public $comment_type =[
        1=>"酷讯",
        2=>"活动"
    ];

    const ARTICLE = 1;
    const ACTIVITY = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'comment_like';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'comment_id', 'type','created_at', 'updated_at'], 'integer'],
            [['created_at', 'updated_at'], 'required']
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
            'comment_id' => '评论id',
            'type' => '评论类型',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
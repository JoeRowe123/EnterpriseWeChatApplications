<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "article_category".
 *
 * @property string $id
 * @property string $name 分类名
 * @property int $status 状态
 * @property int $type 文章类型；1=>天一酷讯;2=>微学堂;
 * @property string $p_id 上级分类
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 *
 * @property ArticleCategory $p
 * @property ArticleCategory[] $articleCategories
 */
class ArticleCategory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public $html;
    public $level;
    public $tree_name;
    public static $tree = [];

    public static function makeCategory($categorys, $type = 'list', $pid = 0, $level = 0)
    {
        foreach ($categorys as $k => $category)
        {
            if ($type == 'list')
            {
                if ($category['p_id'] == $pid)
                {
                    $category['html'] = str_repeat("|--", $level);
                    $category['level'] = $level;
                    $category['name'] = str_repeat("　", $level) . $category['html'] . " " . $category['name'];
                    self::$tree[$category['id']] = $category;
                    self::makeCategory($categorys, $type, $category['id'], $level + 1);
                }
            } else
            {
                if ($category['p_id'] == $pid && $level < 2)
                {
                    $category['html'] = str_repeat("|--", $level);
                    $category['level'] = $level;
                    $category['name'] = str_repeat("　", $level) . $category['html'] . " " . $category['name'];
                    self::$tree[$category['id']] = $category;
                    self::makeCategory($categorys, $type, $category['id'], $level + 1);
                }
            }

        }
        return self::$tree;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'article_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['status', 'type', 'p_id', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 10]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '分类名称',
            'status' => '状态',
            'type' => '文章类型；1=>天一酷讯;2=>微学堂;',
            'p_id' => '上级分类',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * @param $model
     * @param $ids
     */
    public static function getTopCategory($model, &$ids)
    {
        $ids[] = $model->id;
        if($model['p_id'] != 0) {
            if($model->p) {
                self::getTopCategory($model->p, $ids);
            }
        }
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getP()
    {
        return $this->hasOne(ArticleCategory::class, ['id' => 'p_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArticleCategories()
    {
        return $this->hasMany(ArticleCategory::class, ['p_id' => 'id']);
    }

    public function getFirstCount()
    {
        return $this->hasMany(Article::class, ["first_category_id" => "id"])->innerJoin("article_read_object u","u.article_id=article.id")->where(["u.user_id"=>Yii::$app->session->get('userid'),"status"=>10])->select("first_category_id,article.id");
    }

    public function getSecondCount()
    {
        return $this->hasMany(Article::class, ["second_category_id" => "id"])->innerJoin("article_read_object u","u.article_id=article.id")->andFilterWhere(["u.user_id"=>Yii::$app->session->get('userid'),"status"=>10])->select("second_category_id");
    }

    public function getThirdCount()
    {
        return $this->hasMany(Article::class, ["third_category_id" => "id"])->innerJoin("article_read_object u","u.article_id=article.id")->andFilterWhere(["u.user_id"=>Yii::$app->session->get('userid'),"status"=>10])->select("third_category_id");
    }
}

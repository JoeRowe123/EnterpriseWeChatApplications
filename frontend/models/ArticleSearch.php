<?php
/**
 * Copyright(c) 2018-2050,BWSC.Co.Ltd.
 * Created by PhpStorm.
 * User: JoeRowe
 * Date: 2019/11/8 0008
 * Time: 10:48
 *
 */

namespace frontend\models;


use common\models\Article;
use common\models\ArticleCategory;
use yii\data\ActiveDataProvider;

class ArticleSearch extends Article
{
    /**
     * @param $data
     * @return array
     */
    public function search($data)
    {
        $query = Article::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query->with(['like',"first","second","third"])->asArray(),
            "pagination" => [
                'defaultPageSize' => 10
            ]
        ]);

        $query->select(['article.id','article.image','article.title','article.author_name','article.like_number','article.first_category_id','article.second_category_id','article.third_category_id','article.is_important_msg','article.created_at'])->innerJoin("article_read_object","article_read_object.article_id=article.id");
        $query->andFilterWhere(["article_read_object.user_id" => \Yii::$app->session->get('userid')]);

        $query->andFilterWhere([
            'status' => 10,
            "type" => $data['type']
        ]);

        if (!empty($data['category_id'])&&!empty($data['level']))
        {
            switch ($data['level'])
            {
                case 1:{
                    $query->andFilterWhere(["first_category_id" => $data['category_id']]);
                    break;
                }
                case 2:{
                    $query->andFilterWhere(["second_category_id" => $data['category_id']]);
                    break;
                }
                default:
                {
                    $query->andFilterWhere(["third_category_id" => $data['category_id']]);
                }
            }
        }

        if (isset($data['is_read']))
        {
            $query->andFilterWhere([
                "article_read_object.is_read" => $data['is_read']
            ]);
        }

        if (!empty($data['keywords']))
        {
            $query->andFilterWhere(['like', 'title', $data['keywords']]);
        }
        if (!empty($data['start_time']))
        {
            $query->andFilterWhere(['>=', 'article.created_at', strtotime($data['start_time'])]);
        }
        if (!empty($data['end_time']))
        {
            $query->andFilterWhere(['<=', 'article.created_at', strtotime($data['end_time'])]);
        }

        if (isset($data['is_index']))
        {
            $query->andFilterWhere([">=","article.created_at" ,time()-3600*24*7]);
        }

        $query->orderBy('created_at desc');

        //var_dump($query->createCommand()->getRawSql());die;
        return ["total"=>$query->count(),"data"=>$dataProvider->getModels()];
    }
}
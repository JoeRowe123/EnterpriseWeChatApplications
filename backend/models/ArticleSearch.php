<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Article;

/**
 * ArticleSearch represents the model behind the search form of `common\models\Article`.
 */
class ArticleSearch extends Article
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'reading_time', 'is_push_msg', 'is_important_msg', 'status', 'author_id', 'third_category_id', 'updated_at', 'type', 'is_secrecy', 'second_category_id', 'first_category_id'], 'integer'],
            [['title', 'image', 'abstract', 'content', 'author_name', 'created_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Article::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'defaultPageSize' => 20
            ],
            'sort' => [
                'defaultOrder' => [
                    'id'=> SORT_DESC
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'reading_time' => $this->reading_time,
            'is_push_msg' => $this->is_push_msg,
            'is_important_msg' => $this->is_important_msg,
            'status' => $this->status,
            'author_id' => $this->author_id,
            'third_category_id' => $this->third_category_id,
            'second_category_id' => $this->second_category_id,
            'first_category_id' => $this->first_category_id,
            'updated_at' => $this->updated_at,
            'type' => $this->type,
            'is_secrecy' => $this->is_secrecy,
        ]);

        if($this->created_at) {
            $query->andFilterWhere(["between", 'created_at', strtotime($this->created_at. " 00:00:00"), strtotime($this->created_at. " 23:59:59")]);
        }

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'image', $this->image])
            ->andFilterWhere(['like', 'abstract', $this->abstract])
            ->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'author_name', $this->author_name]);

        return $dataProvider;
    }
}

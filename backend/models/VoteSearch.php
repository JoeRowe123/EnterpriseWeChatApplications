<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Vote;

/**
 * VoteSearch represents the model behind the search form of `common\models\Vote`.
 */
class VoteSearch extends Vote
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'type', 'option_type', 'multiple_num', 'vote_type', 'is_repetition', 'is_view', 'is_notice', 'status', 'author_id', 'created_at', 'updated_at', 'total_num'], 'integer'],
            [['title', 'image', 'options', 'author_name'], 'safe'],
            [['start_time'], 'definedRule', 'skipOnEmpty' => false, 'skipOnError' => false],
            [['end_time'], 'definedRule', 'skipOnEmpty' => false, 'skipOnError' => false],
        ];
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function definedRule($attribute, $params)
    {
        if ($this->start_time && $this->end_time)
        {
            if ($this->start_time > $this->end_time)
                Yii::$app->session->setFlash("error","开始时间不能大于结束时间。");
                $this->addError($attribute, "开始时间不能大于结束时间。");
            return;
        }
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
        $query = Vote::find();

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
            'type' => $this->type,
            'option_type' => $this->option_type,
            'multiple_num' => $this->multiple_num,
            'vote_type' => $this->vote_type,
            'is_repetition' => $this->is_repetition,
            'is_view' => $this->is_view,
            'is_notice' => $this->is_notice,
            'status' => $this->status,
            'author_id' => $this->author_id,
            'total_num' => $this->total_num,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        if($this->start_time) {
            $query->andFilterWhere(['>=', 'start_time', strtotime($this->start_time. ' 00:00:00')]);
        }

        if($this->end_time) {
            $query->andFilterWhere(['<=', 'end_time', strtotime($this->end_time. ' 23:59:59')]);
        }

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'image', $this->image])
            ->andFilterWhere(['like', 'options', $this->options])
            ->andFilterWhere(['like', 'author_name', $this->author_name]);

        return $dataProvider;
    }
}

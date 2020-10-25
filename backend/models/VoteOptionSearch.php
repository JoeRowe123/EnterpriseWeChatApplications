<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\VoteOption;

/**
 * VoteOptionSearch represents the model behind the search form of `common\models\VoteOption`.
 */
class VoteOptionSearch extends VoteOption
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'vote_id', 'num'], 'integer'],
            [['option_name', 'option_image'], 'safe'],
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
        $query = VoteOption::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
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
            'num' => $this->num,
            'vote_id' => $this->vote_id,
        ]);

        $query->andFilterWhere(['like', 'option_name', $this->option_name])
            ->andFilterWhere(['like', 'option_image', $this->option_image]);

        return $dataProvider;
    }
}

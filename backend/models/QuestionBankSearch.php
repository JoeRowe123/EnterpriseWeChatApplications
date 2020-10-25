<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\QuestionBank;

/**
 * QuestionBankSearch represents the model behind the search form of `common\models\QuestionBank`.
 */
class QuestionBankSearch extends QuestionBank
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'single_num', 'multiple_num', 'judge_num', 'gap_filling_num', 'total_num', 'author_id', 'status', 'updated_at'], 'integer'],
            [['sn', 'name', 'author_name', 'created_at'], 'safe'],
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
        $query = QuestionBank::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'defaultPageSize' => 20
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_at'=> SORT_DESC
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
            'single_num' => $this->single_num,
            'multiple_num' => $this->multiple_num,
            'judge_num' => $this->judge_num,
            'gap_filling_num' => $this->gap_filling_num,
            'total_num' => $this->total_num,
            'author_id' => $this->author_id,
            'status' => $this->status,
            'updated_at' => $this->updated_at,
        ]);

        if($this->created_at) {
            $query->andFilterWhere(["between", 'created_at', strtotime($this->created_at. " 00:00:00"), strtotime($this->created_at. " 23:59:59")]);
        }

        $query->andFilterWhere(['like', 'sn', $this->sn])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'author_name', $this->author_name]);

        return $dataProvider;
    }
}

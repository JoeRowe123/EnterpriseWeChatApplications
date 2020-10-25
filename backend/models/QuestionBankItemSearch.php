<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\QuestionBankItem;

/**
 * QuestionBankItemSearch represents the model behind the search form of `common\models\QuestionBankItem`.
 */
class QuestionBankItemSearch extends QuestionBankItem
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'type', 'sort', 'grade', 'created_at', 'updated_at', 'status'], 'integer'],
            [['title', 'options', 'answer', 'bank_id'], 'safe'],
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
    public function search($params, $pageSize = 15)
    {
        $query = QuestionBankItem::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'defaultPageSize' => $pageSize,
                'page'            => $params && isset($params['page']) ? $params['page'] - 1 : 0
            ],
            'sort' => [
                'defaultOrder' => [
                    'sort'=> SORT_ASC
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->with("questionBank")->asArray();
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'bank_id' => $this->bank_id,
            'type' => $this->type,
            'sort' => $this->sort,
            'status' => $this->status,
            'grade' => $this->grade,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'options', $this->options])
            ->andFilterWhere(['like', 'answer', $this->answer]);

        return $dataProvider;
    }
}

<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ExaminationPaperItem;

/**
 * ExaminationPaperItemSearch represents the model behind the search form of `common\models\ExaminationPaperItem`.
 */
class ExaminationPaperItemSearch extends ExaminationPaperItem
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'paper_id', 'bank_id', 'item_id', 'item_type', 'item_grade', 'sort', 'created_at', 'updated_at'], 'integer'],
            [['bank_name', 'item_title'], 'safe'],
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
        $query = ExaminationPaperItem::find();

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
            'paper_id' => $this->paper_id,
            'bank_id' => $this->bank_id,
            'item_id' => $this->item_id,
            'item_type' => $this->item_type,
            'item_grade' => $this->item_grade,
            'sort' => $this->sort,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'bank_name', $this->bank_name])
            ->andFilterWhere(['like', 'item_title', $this->item_title]);

        return $dataProvider;
    }
}

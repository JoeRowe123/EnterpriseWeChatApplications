<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ExaminationUsers;

/**
 * ExaminationUsersSearch represents the model behind the search form of `common\models\ExaminationUsers`.
 */
class ExaminationUsersSearch extends ExaminationUsers
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'paper_id', 'start_at', 'end_at', 'total_time', 'grade', 'status', 'is_join', 'created_at', 'updated_at'], 'integer'],
            [['user_id', 'username', 'department_id', 'department_name', 'position', 'phone'], 'safe'],
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
        $query = ExaminationUsers::find();

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
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
            'total_time' => $this->total_time,
            'grade' => $this->grade,
            'status' => $this->status,
            'is_join' => $this->is_join,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'user_id', $this->user_id])
            ->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'department_id', $this->department_id])
            ->andFilterWhere(['like', 'department_name', $this->department_name])
            ->andFilterWhere(['like', 'position', $this->position])
            ->andFilterWhere(['like', 'phone', $this->phone]);

        return $dataProvider;
    }
}

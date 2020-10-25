<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ExaminationPaper;

/**
 * ExaminationPaperSearch represents the model behind the search form of `common\models\ExaminationPaper`.
 */
class ExaminationPaperSearch extends ExaminationPaper
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'type', 'duration_time', 'is_notice', 'is_remind', 'pass_mark', 'status', 'total_grade', 'participant_num', 'not_participant_num', 'total_num', 'author_id', 'created_at', 'updated_at'], 'integer'],
            [['sn', 'name', 'question_bank_ids', 'explain', 'author_name'], 'safe'],
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
        $query = ExaminationPaper::find();

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
            'duration_time' => $this->duration_time,
            'is_notice' => $this->is_notice,
            'is_remind' => $this->is_remind,
            'pass_mark' => $this->pass_mark,
            'status' => $this->status,
            'total_grade' => $this->total_grade,
            'participant_num' => $this->participant_num,
            'not_participant_num' => $this->not_participant_num,
            'total_num' => $this->total_num,
            'author_id' => $this->author_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        if($this->start_time) {
            $query->andFilterWhere(['>=', 'start_time', strtotime($this->start_time. ' 00:00:00')]);
        }

        if($this->end_time) {
            $query->andFilterWhere(['<=', 'end_time', strtotime($this->end_time. ' 23:59:59')]);
        }


        $query->andFilterWhere(['like', 'sn', $this->sn])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'question_bank_ids', $this->question_bank_ids])
            ->andFilterWhere(['like', 'explain', $this->explain])
            ->andFilterWhere(['like', 'author_name', $this->author_name]);

        return $dataProvider;
    }
}

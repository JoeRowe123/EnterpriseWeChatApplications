<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Activity;

/**
 * ActivitySearch represents the model behind the search form of `common\models\Activity`.
 */
class ActivitySearch extends Activity
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'close_date', 'limit_person_num', 'is_push_msg', 'status', 'apply_num', 'view_num', 'comment_num', 'like_num', 'total_num', 'author_id', 'updated_at'], 'integer'],
            [['title', 'image', 'address', 'content', 'attachment', 'author_name', 'created_at'], 'safe'],
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
        $query = Activity::find();

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
            'close_date' => $this->close_date,
            'limit_person_num' => $this->limit_person_num,
            'is_push_msg' => $this->is_push_msg,
            'status' => $this->status,
            'apply_num' => $this->apply_num,
            'view_num' => $this->view_num,
            'comment_num' => $this->comment_num,
            'like_num' => $this->like_num,
            'total_num' => $this->total_num,
            'author_id' => $this->author_id,
            'updated_at' => $this->updated_at,
        ]);

        if($this->start_time) {
            $query->andFilterWhere(['>=', 'start_time', strtotime($this->start_time. ' 00:00:00')]);
        }

        if($this->end_time) {
            $query->andFilterWhere(['<=', 'end_time', strtotime($this->end_time. ' 23:59:59')]);
        }

        if($this->created_at) {
            $query->andFilterWhere(["between", 'created_at', strtotime($this->created_at. " 00:00:00"), strtotime($this->created_at. " 23:59:59")]);
        }

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'image', $this->image])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'attachment', $this->attachment])
            ->andFilterWhere(['like', 'author_name', $this->author_name]);

        return $dataProvider;
    }
}

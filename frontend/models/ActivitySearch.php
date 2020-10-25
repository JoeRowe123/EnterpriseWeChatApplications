<?php
/**
 * Copyright(c) 2018-2050,BWSC.Co.Ltd.
 * Created by PhpStorm.
 * User: JoeRowe
 * Date: 2019/11/13 0013
 * Time: 15:06
 *
 */

namespace frontend\models;


use common\models\Activity;
use yii\data\ActiveDataProvider;

class ActivitySearch extends Activity
{
    /**
     * @param $data
     * @return array
     */
    public function search($data)
    {
        $query = Activity::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query->with("apply")->asArray(),
            "pagination" => [
                'defaultPageSize' => 10
            ]
        ]);

        $query->select(['activity.id', 'activity.image', 'activity.title', 'activity.status', 'activity.theme', 'activity.author_name', 'activity.address', 'activity.start_time', 'activity.end_time', 'activity.close_date'])->innerJoin("activity_read_object a","activity.id = a.activity_id");

        $query->andFilterWhere(['user_id'=>\Yii::$app->session->get("userid")]);

        $query->andFilterWhere([">", "status", 0]);

        if (isset($data['join']))
        {
            $query->andFilterWhere(["a.apply_status"=>$data['join']]);
        }
        if (isset($data["status"]))
        {
            $query->andFilterWhere(["activity.status" => $data["status"]]);
        }

        if (!empty($data['keywords']))
        {
            $query->andFilterWhere(['like', 'title', $data['keywords']]);
        }
        if (!empty($data['start_time']))
        {
            $query->andFilterWhere(['>=', 'activity.start_time', strtotime($data['start_time'])]);
        }
        if (!empty($data['end_time']))
        {
            $query->andFilterWhere(['<=', 'activity.end_time', strtotime($data['end_time'])]);
        }

        if (isset($data['is_index']))
        {
            $query->andFilterWhere([">=","activity.created_at" ,time()-3600*24*7]);
        }

        $query->orderBy('activity.created_at desc');

        //var_dump($query->createCommand()->getRawSql());die;

        return ["total"=>$query->count(),"data"=>$dataProvider->getModels()];
    }
}
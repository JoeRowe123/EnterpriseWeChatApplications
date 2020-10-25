<?php
/**
 * Copyright(c) 2018-2050,BWSC.Co.Ltd.
 * Created by PhpStorm.
 * User: JoeRowe
 * Date: 2019/11/18 0018
 * Time: 15:17
 *
 */

namespace frontend\models;


use common\models\Vote;
use yii\data\ActiveDataProvider;
use yii\db\Query;

class VoteSearch extends Vote
{
    /**
     * @param $data
     * @return array
     */
    public function search($data)
    {
        $query = Vote::find();
        $dataProvider = new ActiveDataProvider([
            "query" => $query->with('isVote')->asArray(),
            'pagination' => [
                'defaultPageSize' => 10
            ]
        ]);

        $query->select(["vote.id","vote.title","vote.image","vote.status","vote.start_time","vote.end_time","vote.author_name","vote.created_at","(select count(user_id) from vote_record where vote_record.vote_id = vote.id) vote_num","(CASE WHEN `status` = 10 || `status` = 20 THEN vote.created_at WHEN `status` = 30 THEN vote.created_at * -1 END) field"]);

        $query->innerJoin("vote_user_object","vote_user_object.vote_id=vote.id")
            ->andFilterWhere(["vote_user_object.user_id"=>\Yii::$app->session->get('userid')]);

        $query->andFilterWhere([">", "status", 0]);

        //已投票
        if (isset($data['is_vote'])&&$data['is_vote']==1)
        {
            $query->andFilterWhere(["in" ,'vote.id',(new Query())->select('vote_id')->distinct('vote_id')->from('vote_record')->where(["user_id"=>\Yii::$app->session->get('uid')])]);
        }
        if (!empty($data['status']))
        {
            $query->andFilterWhere(["status" => $data['status']]);
        }

        //未投票
        if (isset($data['is_vote']) && $data['is_vote'] == 2)
        {
            $query->andFilterWhere(["not in" ,'vote.id',(new Query())->select('vote_id')->distinct('vote_id')->from('vote_record')->where(["user_id"=>\Yii::$app->session->get('uid')])]);
        }


        if (!empty($data['keywords']))
        {
            $query->andFilterWhere(['like', 'title', $data['keywords']]);
        }
        if (!empty($data['start_time']))
        {
            $query->andFilterWhere(['>=', 'vote.start_time', strtotime($data['start_time'])]);
        }
        if (!empty($data['end_time']))
        {
            $query->andFilterWhere(['<=', 'vote.end_time', strtotime($data['end_time'])]);
        }

        if (isset($data['is_index']))
        {
            $query->andFilterWhere([">=","vote.created_at" ,time()-3600*24*7]);
        }

        //$query->orderBy('status asc,field asc');
        $query->orderBy('created_at desc');
        //var_dump($query->createCommand()->getRawSql());die;
        return ["total"=>$query->count(),"data"=>$dataProvider->getModels()];
    }
}
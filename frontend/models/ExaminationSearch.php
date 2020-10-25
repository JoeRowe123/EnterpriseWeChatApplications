<?php
/**
 * Copyright(c) 2018-2050,BWSC.Co.Ltd.
 * Created by PhpStorm.
 * User: JoeRowe
 * Date: 2019/11/19 0019
 * Time: 15:50
 *
 */

namespace frontend\models;


use common\models\ExaminationPaper;
use yii\data\ActiveDataProvider;
use yii\db\Query;

class ExaminationSearch extends ExaminationPaper
{
    public function search($data)
    {
        $query = ExaminationPaper::find();

        $dataProvider = new ActiveDataProvider([
            "query" => $query->with(['examInfo'])->asArray(),
            "pagination" => [
                'defaultPageSize' => 10
            ]
        ]);

        $query->innerJoin("examination_users u","u.paper_id = examination_paper.id")->where(["u.user_id"=>\Yii::$app->session->get('userid')]);


        $query->andFilterWhere([
            ">", "examination_paper.status", 0
        ]);

        //未考试
        if (isset($data['is_exam'])&&$data['is_exam']==1)
        {
            $query->andFilterWhere(['u.is_join'=>0]);
        }
        //已考试
        if (isset($data['is_exam'])&&$data['is_exam']==2)
        {
            $query->andFilterWhere(['u.is_join'=>1]);
        }

        if (!empty($data['is_end']))
        {
            $query->andFilterWhere(['>=',"examination_paper.end_time",time()]);
        }

        //考核状态
        if (isset($data['is_pass']))
        {
            $query->andFilterWhere(["u.status"=>$data['is_pass']]);
        }

        if (!empty($data['keywords']))
        {
            $query->andFilterWhere(['like', 'name', $data['keywords']]);
        }
        if (!empty($data['start_time']))
        {
            $query->andFilterWhere(['>=', 'start_time', strtotime($data['start_time'])]);
        }
        if (!empty($data['end_time']))
        {
            $query->andFilterWhere(['<=', 'end_time', strtotime($data['end_time'])]);
        }

        if (isset($data['is_index']))
        {
            $query->andFilterWhere([">=", "start_time", time() - 3600 * 24 * 7]);
        }

        $query->orderBy('examination_paper.created_at desc');
        //var_dump($query->createCommand()->getRawSql());die;
        return ["total" => $query->count(), "data" => $dataProvider->getModels()];
    }
}
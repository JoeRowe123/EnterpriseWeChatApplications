<?php
/**
 * Copyright(c) 2018-2050,BWSC.Co.Ltd.
 * Created by PhpStorm.
 * User: JoeRowe
 * Date: 2019/11/18 0018
 * Time: 15:17
 *
 */

namespace frontend\controllers;


use common\models\Vote;
use common\models\VoteOption;
use common\models\VoteRecord;
use common\models\VoteUserObject;
use frontend\models\VoteSearch;
use yii\data\ActiveDataProvider;
use yii\db\Exception;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

class VoteController extends BaseController
{
    /**
     * @return array
     */

    public function actionList()
    {
        $data = \Yii::$app->request->queryParams;
        $model = new VoteSearch();
        $lists = $model->search($data);
        return $lists;
    }

    /**
     * @param $id
     * @return array|null|\yii\db\ActiveRecord
     */
    public function actionDetail($id)
    {
        if (!VoteUserObject::findOne(['vote_id' => $id,"user_id"=>\Yii::$app->session->get("userid")]))
        {
            throw new HttpException(402, "您没有访问权限");
        }
        $model = Vote::find()
            ->select(["*","(select count(DISTINCT user_id) from vote_record where vote_record.vote_id = vote.id) vote_num"])
            ->with(['voteRecords'])
            ->where(["id" => $id])
            ->asArray()
            ->one();
        if (!$model)
        {
            throw new NotFoundHttpException("对应内容已被删除");
        }

        return $model;
    }

    /**
     * @throws \yii\console\Exception
     */
    public function actionVote()
    {
        $voteInfo = Vote::findOne(['id'=>\Yii::$app->request->post('vote_id')]);
        if (time() > $voteInfo['end_time'])
        {
            throw new HttpException(500, "投票已结束，无法投票");
        }
        $model = new VoteRecord();
        $options = VoteOption::find()
            ->select("option_name")
            ->where(['in','id',\Yii::$app->request->post('option_ids')])
            ->asArray()
            ->all();
        if (\Yii::$app->request->isPost)
        {
            $model->load(\Yii::$app->request->post());
            $transaction  = \Yii::$app->db->beginTransaction();
            try
            {
                $model->user_id = \Yii::$app->session->get('uid');
                $model->username = \Yii::$app->session->get('username');
                $model->vote_id = \Yii::$app->request->post('vote_id');
                $model->option_ids = \Yii::$app->request->post('option_ids');
                $model->desc = array_column($options,"option_name");
                $model->created_at = time();
                $model->updated_at = time();
                if (!$model->save())
                {
                    throw new Exception("投票纪录保存失败");
                }
                if (!VoteOption::updateAllCounters(["num"=>1],['in','id',\Yii::$app->request->post('option_ids')])){
                    throw new Exception("投票数修改失败");
                }
                $transaction->commit();
            } catch (Exception $e)
            {
                $transaction->rollBack();
                throw new Exception($e->getMessage());
            }
            return ["retMsg" => "投票成功"];
        }
        throw new NotFoundHttpException("请求方式错误");
    }

    /**
     * @param $vote_id
     * @param $keywords
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionOptionSearch($vote_id, $keywords)
    {
        $query= VoteOption::find()->asArray();
        $dataProvider = new ActiveDataProvider([
            "query" => $query,
            'pagination' => [
                'defaultPageSize' => 10
            ]
        ]);
        
        $query->andFilterWhere(["vote_id"=>$vote_id])
            ->andFilterWhere(["like","option_name",$keywords]);
        $query->orderBy("num desc,id asc");
        return ['total'=>$query->count(),'data'=>$dataProvider->getModels(),"totalNum"=>$query->sum("num")];
    }
}
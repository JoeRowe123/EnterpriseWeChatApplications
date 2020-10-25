<?php
/**
 * Copyright(c) 2018-2050,BWSC.Co.Ltd.
 * Created by PhpStorm.
 * User: JoeRowe
 * Date: 2019/11/13 0013
 * Time: 11:00
 *
 */

namespace frontend\controllers;


use common\models\Activity;
use common\models\ActivityApplyInfo;
use common\models\ActivityComment;
use common\models\ActivityItem;
use common\models\ActivityLike;
use common\models\ActivityReadObject;
use common\models\CommentLike;
use frontend\models\ActivitySearch;
use yii\console\Exception;
use yii\db\Expression;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

class ActivityController extends BaseController
{
    /**
     * @return array
     */
    public function actionList()
    {
        $param = \Yii::$app->request->queryParams;
        $model = new ActivitySearch();
        $dataProvider = $model->search($param);
        return $dataProvider;
    }

    /**
     * @param $id
     * @return array|null|\yii\db\ActiveRecord
     * @throws NotFoundHttpException
     */
    public function actionDetail($id)
    {
        $auth = ActivityReadObject::findOne(['activity_id' => $id,"user_id"=>\Yii::$app->session->get("userid")]);
        if (!$auth)
        {
            throw new HttpException(402, "您没有访问权限");
        }

        $model = Activity::find()
            ->with(["comment" => function ($query)
            {
                $query->with(["children" => function ($query)
                {
                    $query->with("quest");
                }, "like"]);
            },
                "like",
                "apply"])
            ->where(["id" => $id])
            ->andFilterWhere([">", "status", 0])
            ->asArray()
            ->one();
        if (!$model)
        {
            throw new NotFoundHttpException("对应内容已被删除");
        }

       ActivityReadObject::updateAll(['is_read' => 1, "read_time" => time()], ['activity_id' => $id,"user_id"=>\Yii::$app->session->get("userid")]);
        //查看增加
        Activity::updateAll(['view_num' => ActivityReadObject::find()->where(['is_read' => 1, "activity_id" => $id])->count()],
            ['id' => $id]);

        return $model;
    }

    /**
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionComment()
    {
        $model = new ActivityComment();
        $model->loadDefaultValues();
        $model->load(\Yii::$app->request->post());
        $model->user_id = \Yii::$app->session->get('uid');
        $model->username = \Yii::$app->session->get("username");
        $model->activity_id = \Yii::$app->request->post('activity_id');
        $model->content = \Yii::$app->request->post('content');
        $model->pid = \Yii::$app->request->post('pid');
        $model->rid = \Yii::$app->request->post('pid');
        $model->del = 1;
        $model->created_at = time();
        $model->updated_at = time();
        if ($model->save())
        {
            Activity::updateAllCounters(["comment_num" => 1], ['id' => \Yii::$app->request->post('activity_id')]);
            return ["retCode" => 0, "retMsg" => "操作成功"];
        } else
        {
            throw new \yii\db\Exception("操作失败");
        }
    }

    /**
     * @param $id
     * @return array
     */
    public function actionDelComment($id)
    {
        //$info = ActivityComment::findOne(["id" => $id]);
        if (ActivityComment::updateAll(["del" => 0], ["id" => $id]))
        {
            //Activity::updateAllCounters(["comment_num" => -1], ['id' => $info['activity_id']]);
            return ["retCode" => 0, "retMsg" => "操作成功"];
        } else
        {
            throw new \yii\db\Exception("操作失败");
        }

    }

    /**活动喜欢
     * @param $id
     * @return array
     */
    public function actionActivityLike($id)
    {
        $model = new ActivityLike();
        $model->user_id = \Yii::$app->session->get('uid');
        $model->username = \Yii::$app->session->get("username");
        $model->activity_id = $id;
        $model->created_at = time();
        $model->updated_at = time();
        if ($model->save())
        {
            Activity::updateAllCounters(["like_num" => 1], ['id' => $id]);
            return ["retCode" => 0, "retMsg" => "操作成功"];
        } else
        {
            throw new \yii\db\Exception("操作失败");
        }
    }

    /**
     * @param $article_id
     * @return array
     */
    public function actionCancelActivityLike($activity_id)
    {
        $like = ActivityLike::find()
            ->where(['activity_id' => $activity_id, 'user_id' => \Yii::$app->session->get('uid')])
            ->one();
        if (!$like)
        {
            throw new NotFoundHttpException("无点赞记录");
        }
        if ($like->delete())
        {
            Activity::updateAllCounters(["like_num" => -1], ['id' => $activity_id]);
            return ["retCode" => 0, "retMsg" => "操作成功"];
        } else
        {
            throw new \yii\db\Exception("操作失败");
        }
    }

    /**
     * @param $comment_id
     * @param $article_id
     * @return array
     */
    public function actionCommentLike($comment_id)
    {
        $model = new CommentLike();
        $model->user_id = \Yii::$app->session->get('uid');
        $model->type = CommentLike::ACTIVITY;
        $model->comment_id = $comment_id;
        $model->created_at = time();
        $model->updated_at = time();
        if ($model->save())
        {
            return ["retCode" => 0, "retMsg" => "操作成功"];
        } else
        {
            throw new \yii\db\Exception("操作失败");
        }
    }

    /**
     * @param $comment_id
     * @return array
     */
    public function actionCancelCommentLike($comment_id)
    {
        $model = CommentLike::find()->where(['comment_id' => $comment_id, 'type' => CommentLike::ACTIVITY, 'user_id' => \Yii::$app->session->get('uid')])->one();
        if (!$model)
        {
            throw new NotFoundHttpException("无点赞记录");
        }
        if ($model->delete())
        {
            return ["retCode" => 0, "retMsg" => "操作成功"];
        } else
        {
            throw new \yii\db\Exception("操作失败");
        }
    }

    /**活动选项
     * @param $activity_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionActivityItem($activity_id)
    {
        $model = ActivityItem::find()
            ->where(['activity_id' => $activity_id])
            ->all();
        return $model;
    }

    /**
     * @return array
     */
    public function actionApply()
    {
        $activity = Activity::findOne(["id" => \Yii::$app->request->post('activity_id')]);
        if (time() > $activity['close_date'])
        {
            throw new HttpException(500, "该活动报名时间已过");
        }

        if ($activity['limit_person_num'] > $activity["apply_num"] || $activity['limit_person_num'] == 0)
        {
            $model = ActivityReadObject::find()->where([
                'user_id' => \Yii::$app->session->get('userid'),
                'activity_id' => \Yii::$app->request->post('activity_id')
            ])->one();
            if ($model->apply_status == 1)
            {
                throw new \yii\db\Exception("已报名，请勿重复操作");
            }
            $model->apply_status = 1;
            $model->apply_options = \Yii::$app->request->post("options");
            $model->apply_at = time();
            $model->updated_at = time();
            $transaction = \Yii::$app->db->beginTransaction();
            try
            {
                if (!$model->save())
                {
                    throw new \yii\db \Exception("报名失败");
                }
                if (!Activity::updateAllCounters(["apply_num" => 1], ['id' => \Yii::$app->request->post('activity_id')]))
                {
                    throw new \yii\db\Exception("修改已报名人数失败");
                }
                $transaction->commit();
            } catch (\yii\db\Exception $e)
            {
                $transaction->rollBack();
                throw new \yii\db\Exception($e->getMessage());
            }
            return ["retMsg" => "报名成功"];
        }
        throw new Exception("报名人数已达上限");
    }

    /**取消活动报名
     * @return array
     */
    public function actionRevoke($activity_id)
    {
        $model = ActivityReadObject::find()->where([
            'user_id' => \Yii::$app->session->get('userid'),
            'activity_id' => $activity_id
        ])->one();
        if ($model->apply_status != 1)
        {
            throw new \yii\db\Exception("尚未报名，无法进行操作");
        }
        $model->apply_status = -1;
        $model->updated_at = time();
        $transaction = \Yii::$app->db->beginTransaction();
        try
        {
            if (!$model->save())
            {
                throw new \yii\db\Exception("取消报名失败");
            }
            if (!Activity::updateAllCounters(["apply_num" => -1], ['id' => $activity_id]))
            {
                throw new \yii\db\Exception("修改已报名人数失败");
            }
            $transaction->commit();
        } catch (\yii\db\Exception $e)
        {
            $transaction->rollBack();
            throw new \yii\db\Exception($e->getMessage());
        }
        return ["retCode" => 0, "retMsg" => "操作成功"];
    }

    /**
     * @param $activity_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionEntryList($activity_id)
    {
        $model = ActivityReadObject::find()->where([
            'activity_id' => $activity_id,
            "apply_status" => 1
        ])->all();
        return $model;
    }

    /**
     * @param $activity_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionInvite($activity_id)
    {
        $model = ActivityReadObject::find()
            ->select(["department_name", "department_id", "username", "id", "avatar"])
            ->where([
                "activity_id" => $activity_id
            ])
            ->asArray()
            ->all();
        $data = [];

        foreach ($model as $val)
        {
            $department = json_decode($val['department_name']);
            foreach ($department as $item)
            {
                $data[$item->id]['department_name'] = $item->name;
                $data[$item->id]['department_id'] = $item->id;
                $data[$item->id]['member'][] = ["username" => $val['username'], "avatar" => $val['avatar']];
            }
        }
        $group = [];
        foreach ($data as $v)
        {
            $group[] = $v;
        }
        return $group;
    }

}
<?php
/**
 * Copyright(c) 2018-2050,BWSC.Co.Ltd.
 * Created by PhpStorm.
 * User: JoeRowe
 * Date: 2019/11/19 0019
 * Time: 15:48
 *
 */

namespace frontend\controllers;


use common\components\wework\WeworkSendMsg;
use common\models\ExaminationAnswer;
use common\models\ExaminationPaper;
use common\models\ExaminationPaperItem;
use common\models\ExaminationUsers;
use frontend\models\ExaminationSearch;
use yii\db\Exception;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

class ExamineController extends BaseController
{
    /**
     * @return array
     */
    public function actionList()
    {
        $data = \Yii::$app->request->queryParams;
        $model = new ExaminationSearch();
        $dataProvider = $model->search($data);
        return $dataProvider;
    }

    public function actionPaperInfo($id)
    {
        $model = ExaminationPaper::find()
            ->with(['examInfo'])
            ->innerJoin("examination_users u","u.paper_id = examination_paper.id")->where(["u.user_id"=>\Yii::$app->session->get('userid')])
            ->andFilterWhere(['examination_paper.id'=>$id])
            ->asArray()
            ->one();
        if (!$model) {
            throw new NotFoundHttpException("对应内容已被删除");
        }

        return $model;
    }

    /**
     * @param $paper_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionPaperItems($paper_id)
    {
        $model = ExaminationPaperItem::find()
            ->select("id,item_title,item_type,item_grade,item_option")
            ->where(["paper_id" => $paper_id])
            ->orderBy("sort asc")
            ->asArray()
            ->all();
        return $model;
    }

    /**答题
     * @return string
     * @throws \Exception
     */
    public function actionAnswer()
    {
        $data = \Yii::$app->request->post("answer");
        $paper = ExaminationPaper::find()->Where(["id" => \Yii::$app->request->post("paper_id")])->one();
        if (time() > $paper['end_time'])
        {
            throw new HttpException(500,"考试已结束，无法参与考试");
        }
        $examUsr = ExaminationUsers::findOne(['user_id' => \Yii::$app->session->get('userid'), "paper_id" => \Yii::$app->request->post('paper_id')]);
        if ($examUsr["is_join"] == 1)
        {
            throw new \Exception("考试已完成，请勿重复提交");
        }
        $transaction = \Yii::$app->db->beginTransaction();
        $right_items = 0;   //答对数量
        $pass_status = 0;   //是否通过
        foreach ($data as $k => $v)
        {
            $data[$k]["answer"] = json_encode($v["answer"]);
            $data[$k]["examination_user_id"] = $examUsr["id"];
            $data[$k]["user_id"] = \Yii::$app->session->get('userid');
            $data[$k]["created_at"] = time();
            $data[$k]["updated_at"] = time();
            //自动阅卷
            $itemInfo = ExaminationPaperItem::findOne(["id" => $v["examination_item_id"]]);
            $data[$k]["grade"] = 0;
            switch ($itemInfo['item_type'])
            {
                case 1:
                {
                    if ($itemInfo["item_answer"] == $v["answer"]&&$itemInfo["item_answer"])
                    {
                        $data[$k]["grade"] = $itemInfo["item_grade"];
                        $data[$k]["is_true"] = 1;
                        $right_items++;
                    }else{
                        $data[$k]["is_true"] = 0;
                    }
                    break;
                }
                case 2:
                {
                    if (empty(array_diff($itemInfo["item_answer"], $v["answer"]))&&$itemInfo["item_answer"])
                    {
                        $data[$k]["grade"] = $itemInfo["item_grade"];
                        $data[$k]["is_true"] = 1;
                        $right_items++;
                    } else
                    {
                        $data[$k]["is_true"] = 0;
                    }
                    break;
                }
                case 3:
                {
                    if ($itemInfo["item_answer"] == $v["answer"]&&$itemInfo["item_answer"])
                    {
                        $data[$k]["grade"] = $itemInfo["item_grade"];
                        $data[$k]["is_true"] = 1;
                        $right_items++;
                    }else{
                        $data[$k]["is_true"] = 0;
                    }
                    break;
                }
                default:
                {
                    $pass_status = 2;
                    $data[$k]["is_true"] = -1;
                }
            }
        }
        try
        {
            $ret = \Yii::$app->db->createCommand()->batchInsert(ExaminationAnswer::tableName(),
                ["paper_id", "examination_item_id", "answer", "examination_user_id", "user_id",
                    "created_at", "updated_at","grade","is_true"], $data)->execute();
            if (!$ret)
            {
                throw new Exception("答题失败");
            }
            //及格
            if (array_sum(array_column($data, "grade"))>=$paper["pass_mark"]&&$pass_status<2)
            {
                $pass_status = 1;
            }
            //无填空题出成绩后发送通知
            if ($pass_status<2)
            {
                $title = $pass_status==1 ? "您有考试通过了" : "您有考试未通过";
                //推送消息
                $time = date("Y-m-d H:i:s", $paper['start_time']). ' - '. date("Y-m-d H:i:s", $paper['end_time']);
                $params = [
                    "title" => $title,
                    "description" => "
试卷名称：{$paper['name']}
考试时间：{$time}
发布人：{$paper['author_name']} 
",
                    "url" => \Yii::$app->params['wework']['frontPaperUrl'].$paper['id'],
                    "btntxt" => "查看详情",
                ];
                WeworkSendMsg::send([\Yii::$app->session->get('userid')], [], [], $params);
            }

            $examInsertData = [

                "is_join" => 1,
                "total_time" => \Yii::$app->request->post("total_time"),
                "grade" => array_sum(array_column($data, "grade")),
                "right_items" => $right_items,
                "status" => $pass_status,
                "start_at" => \Yii::$app->request->post("start_time"),
                "end_at" => \Yii::$app->request->post("end_time"),
                "updated_at" => time()
            ];
            if (!ExaminationUsers::updateAll($examInsertData, ["user_id" => \Yii::$app->session->get('userid'), "paper_id" => \Yii::$app->request->post('paper_id')]))
            {
                throw new Exception("考试状态更新失败");
            }

            if (!ExaminationPaper::updateAllCounters(["participant_num" => 1, "not_participant_num" => -1], ["id" => \Yii::$app->request->post("paper_id")]))
            {
                throw new Exception("参与人数更新失败");
            }
            $transaction->commit();
        } catch (Exception $e)
        {
            $transaction->rollBack();
            throw new \Exception($e->getMessage());
        }
        return "考试完成";
    }

    /**
     * @param $paper_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionPaperQuery($paper_id)
    {
        $model = ExaminationPaperItem::find()
            ->with(["myAnswer","examInfo"])
            ->where(["paper_id" => $paper_id])
            ->orderBy("sort asc")
            ->asArray()
            ->all();
        if (!$model)
        {
            throw new NotFoundHttpException("未查询到相关试卷信息");
        }
        return $model;
    }
}
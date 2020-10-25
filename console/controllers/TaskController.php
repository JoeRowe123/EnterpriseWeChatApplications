<?php
namespace console\controllers;

use common\components\wework\WeworkSendMsg;
use common\models\Activity;
use common\models\ActivityReadObject;
use common\models\Article;
use common\models\ArticleReadObject;
use common\models\ExaminationPaper;
use common\models\ExaminationUsers;
use common\models\Vote;
use common\models\VoteUserObject;
use yii\console\Controller;
use yii\db\Exception;

class TaskController extends Controller
{
    /**
     * 试卷状态
     */
    public function actionPaperStatus()
    {
        $trans = \Yii::$app->db->beginTransaction();
        try {
            $ingModels = ExaminationPaper::find()->with('users')->where(['status' => ExaminationPaper::STATUS_NOT_START])->andWhere(["<=", 'start_time', time()])->asArray();
            //考试进行中
            foreach ($ingModels->batch(1000) as $items) {
                foreach ($items as $item) {
                    if(!ExaminationPaper::updateAll(["status" => ExaminationPaper::STATUS_GOING], ["id" => $item['id']])) {
                        throw new Exception("修改ing试卷状态失败");
                    }
                    //推送消息
                    $time = date("Y-m-d H:i:s", $item['start_time']). ' - '. date("Y-m-d H:i:s", $item['end_time']);
                    $params = [
                        "title" => "您有考试已经开始了",
                        "description" => "
试卷名称：{$item['name']}
考试时间：{$time}
发布人：{$item['author_name']}
",
                        "url" => \Yii::$app->params['wework']['frontPaperUrl'].$item['id'],
                        "btntxt" => "查看详情",
                    ];
                    $usersArrIng = array_column($item['users'], "user_id");
                    WeworkSendMsg::send($usersArrIng, [], [], $params);
                    ExaminationUsers::updateAll(["is_receive_msg" => 1], ["paper_id" => $item['id']]);
                }
            }
            $endModels = ExaminationPaper::find()->with('users')->where(['status' => ExaminationPaper::STATUS_GOING])->andWhere(["<=", 'end_time', time()])->asArray();
            //考试结束
            foreach ($endModels->batch(1000) as $models) {
                foreach ($models as $model) {
                    if(!ExaminationPaper::updateAll(["status" => ExaminationPaper::STATUS_END], ["id" => $model['id']])) {
                        throw new Exception("修改end试卷状态失败");
                    }
                    //推送消息
//                    $time = date("Y-m-d H:i:s", $model['start_time']). ' - '. date("Y-m-d H:i:s", $model['end_time']);
//                    $params = [
//                        "title" => "您有考试已经截止了",
//                        "description" => "
//试卷名称：{$model['name']}
//考试时间：{$time}
//发布人：{$model['author_name']}
//",
//                        "url" => \Yii::$app->params['wework']['frontPaperUrl'].$model['id'],
//                        "btntxt" => "查看详情",
//                    ];
//                    $usersArr = array_column($model['users'], "user_id");
//                    WeworkSendMsg::send($usersArr, [], [], $params);
//                    ExaminationUsers::updateAll(["is_receive_msg" => 1], ["paper_id" => $model['id']]);
                }
            }
            $trans->commit();
            echo "执行修改试卷状态成功".PHP_EOL;
        } catch (\Exception $e) {
            $trans->rollBack();
            \Yii::error("执行修改试卷状态任务失败：".$e->getMessage().$e->getTraceAsString());
            echo "执行修改试卷状态任务失败".$e->getMessage().PHP_EOL;
        }

    }

    /**
     * 投票状态
     */
    public function actionVoteStatus()
    {
        $trans = \Yii::$app->db->beginTransaction();
        try {
            $ingModels = Vote::find()->with("users")->where(['status' => Vote::STATUS_NOT_START])->andWhere(["<=", 'start_time', time()])->asArray();
            //投票进行中
            foreach ($ingModels->batch(1000) as $items) {
                foreach ($items as $item) {
                    if(!Vote::updateAll(["status" => Vote::STATUS_GOING], ["id" => $item['id']])) {
                        throw new Exception("修改ing投票状态失败");
                    }
                    //推送消息
                    $time = date("Y-m-d H:i:s", $item['start_time']). ' - '. date("Y-m-d H:i:s", $item['end_time']);
                    $params = [
                        "title" => "投票已经开始了",
                        "description" => "
    标题：{$item['title']}
    投票时间：{$time}
    发布人：{$item['author_name']}
    ",
                        "url" => \Yii::$app->params['wework']['frontVoteUrl'].$item['id'],
                        "btntxt" => "查看详情",
                    ];
                    $usersArrIng = array_column($item['users'], "user_id");
                    WeworkSendMsg::send($usersArrIng, [], [], $params);
                    VoteUserObject::updateAll(["is_receive_msg" => 1], ["vote_id" => $item['id']]);
                }
            }

//            $endModels = Vote::find()->with("users")->where(['status' => Vote::STATUS_GOING])->andWhere(["<=", 'end_time', time()])->asArray();
//            //投票结束
//            foreach ($endModels->batch(1000) as $models) {
//                foreach ($models as $model) {
//                    if(!Vote::updateAll(["status" => Vote::STATUS_END], ["id" => $model['id']])) {
//                        throw new Exception("修改end投票状态失败");
//                    }
//                    //推送消息
//                    $time = date("Y-m-d H:i:s", $model['start_time']). ' - '. date("Y-m-d H:i:s", $model['end_time']);
//                    $params = [
//                        "title" => "投票结束了",
//                        "description" => "
//标题：{$model['title']}
//投票时间：{$time}
//发布人：{$model['author_name']}
//",
//                        "url" => \Yii::$app->params['wework']['frontVoteUrl'].$model['id'],
//                        "btntxt" => "查看详情",
//                    ];
//                    $usersArr = array_column($model['users'], "user_id");
//                    WeworkSendMsg::send($usersArr, [], [], $params);
//                    VoteUserObject::updateAll(["is_receive_msg" => 1], ["vote_id" => $model['id']]);
//                }
//            }
            $trans->commit();
            echo "执行修改投票状态成功".PHP_EOL;
        } catch (\Exception $e) {
            $trans->rollBack();
            \Yii::error("执行修改投票状态任务失败：".$e->getMessage().$e->getTraceAsString());
            echo "执行修改投票状态任务失败".$e->getMessage().PHP_EOL;
        }

    }

    /**
     * 活动状态
     */
    public function actionActivityStatus()
    {
        $trans = \Yii::$app->db->beginTransaction();
        try {
            $ingModels = Activity::find()->with('invite')->where(['status' => Activity::STATUS_NOT_START])->andWhere(["<=", 'start_time', time()])->asArray();
            //活动进行中
            foreach ($ingModels->batch(1000) as $items) {
               foreach ($items as $item) {
                   if(!Activity::updateAll(["status" => Activity::STATUS_GOING], ["id" => $item['id']])) {
                       throw new Exception("修改ing活动状态失败");
                   }
                   //推送消息
                   $time = date("Y-m-d H:i:s", $item['start_time']). ' - '. date("Y-m-d H:i:s", $item['end_time']);
                   $params = [
                       "title" => "活动已经开始了",
                       "description" => "
标题：{$item['title']}
活动时间：{$time}
发布人：{$item['author_name']}
",
                       "url" => \Yii::$app->params['wework']['frontActivityUrl'].$item['id'],
                       "btntxt" => "查看详情",
                   ];
                   $usersArrIng = array_column($item['invite'], "user_id");
                   WeworkSendMsg::send($usersArrIng, [], [], $params);
                   ActivityReadObject::updateAll(["is_receive_msg" => 1], ["activity_id" => $item['id']]);
               }
            }
//            $endModels = Activity::find()->with('invite')->where(['status' => Activity::STATUS_GOING])->andWhere(["<=", 'end_time', time()])->asArray();
//            //活动结束
//            foreach ($endModels->batch(1000) as $models) {
//              foreach ($models as $model) {
//                  if(!Activity::updateAll(["status" => Activity::STATUS_END], ["id" => $model['id']])) {
//                      throw new Exception("修改end活动状态失败");
//                  }
//                  //推送消息
//                  $time = date("Y-m-d H:i:s", $model['start_time']). ' - '. date("Y-m-d H:i:s", $model['end_time']);
//                  $params = [
//                      "title" => "活动结束了",
//                      "description" => "
//标题：{$model['title']}
//活动时间：{$time}
//发布人：{$model['author_name']}
//",
//                      "url" => \Yii::$app->params['wework']['frontActivityUrl'].$model['id'],
//                      "btntxt" => "查看详情",
//                  ];
//                  $usersArr = array_column($model['invite'], "user_id");
//                  WeworkSendMsg::send($usersArr, [], [], $params);
//                  ActivityReadObject::updateAll(["is_receive_msg" => 1], ["activity_id" => $model->id]);
//              }
//            }
            $trans->commit();
            echo "执行修改活动状态成功".PHP_EOL;
        } catch (\Exception $e) {
            $trans->rollBack();
            \Yii::error("执行修改活动状态任务失败：".$e->getMessage().$e->getTraceAsString());
            echo "执行修改活动状态任务失败".$e->getMessage().PHP_EOL;
        }

    }


    /**
     * 投票通知
     */
    public function actionSendVoteMsg()
    {
        try {
            $models = Vote::find()->with('users')->where(['status' => Vote::STATUS_GOING])->asArray();
            foreach ($models->batch(1000) as $models) {
                foreach ($models as $model) {
                    if($model['end_time'] - strtotime(date("Y-m-d H:i:00")) <= 600 && $model['is_ten'] == 0) {
                        //推送消息
                        $time = date("Y-m-d H:i:s", $model['start_time']). ' - '. date("Y-m-d H:i:s", $model['end_time']);
                        $params = [
                            "title" => "投票即将结束，请及时投票",
                            "description" => "
标题：{$model['title']}
投票时间：{$time}
发布人：{$model['author_name']}
",
                            "url" => \Yii::$app->params['wework']['frontVoteUrl'].$model['id'],
                            "btntxt" => "查看详情",
                        ];
                        $usersArr = array_column($model['users'], "user_id");
                        WeworkSendMsg::send($usersArr, [], [], $params);
                        Vote::updateAll(["is_ten" => 1], ["id" => $model['id']]);
                    }
                }
            }
            echo "执行发送投票通知成功".PHP_EOL;
        } catch (\Exception $e) {
            echo "执行发送投票通知失败".$e->getMessage().PHP_EOL;
        }

    }

    /**
     * 活动通知
     */
    public function actionSendActivityMsg()
    {
        try {
            $models = Activity::find()->with('invite')->asArray();
            foreach ($models->batch(1000) as $models) {
                foreach ($models as $model) {
                    $title = "";
                    $flag = 0;
                    if($model['close_date'] - strtotime(date("Y-m-d H:i:00")) <= 600 && $model['is_ten'] == 0) {
                        $title = "活动报名时间即将截止，请及时报名";
                        $flag = 1;
                    } elseif ($model['start_time'] - strtotime(date("Y-m-d H:i:00")) <= 1800 && $model['is_thirty'] == 0) {
                        $title =  "活动即将开始";
                        $flag = 2;
                    }
                    if($title) {
                        //推送消息
                        $time = date("Y-m-d H:i:s", $model['start_time']). ' - '. date("Y-m-d H:i:s", $model['end_time']);
                        $params = [
                            "title" => $title,
                            "description" => "
标题：{$model['title']}
活动时间：{$time}
发布人：{$model['author_name']}
",
                            "url" => \Yii::$app->params['wework']['frontActivityUrl'].$model['id'],
                            "btntxt" => "查看详情",
                        ];
                        $usersArr = array_column($model['invite'], "user_id");
                        WeworkSendMsg::send($usersArr, [], [], $params);
                        if($flag == 1) {
                            Activity::updateAll(["is_ten" => 1], ["id" => $model['id']]);
                        } else if($flag == 2) {
                            Activity::updateAll(["is_thirty" => 1], ["id" => $model['id']]);
                        }
                    }
                }
            }
            echo "执行发送活动通知成功".PHP_EOL;
        } catch (\Exception $e) {
            echo "执行发送活动通知失败".$e->getMessage().PHP_EOL;
        }

    }

    /**
     * 考试通知
     */
    public function actionSendPaperMsg()
    {
        try {
            $models = ExaminationPaper::find()->with('users')->where(['status' => ExaminationPaper::STATUS_GOING])->asArray();
            foreach ($models->batch(1000) as $models) {
               foreach ($models as $model) {
                   $title = "";
                   if ($model['end_time'] - strtotime(date("Y-m-d H:i:00")) <= 3600 && $model['is_an_hour'] == 0) {
                       $title =  "您有考试即将截止，请及时参加考试";
                   }
                   if($title) {
                       //推送消息
                       $time = date("Y-m-d H:i:s", $model['start_time']). ' - '. date("Y-m-d H:i:s", $model['end_time']);
                       $params = [
                           "title" => $title,
                           "description" => "
试卷名称：{$model['title']}
考试时间：{$time}
发布人：{$model['author_name']}
",
                           "url" => \Yii::$app->params['wework']['frontPaperUrl'].$model['id'],
                           "btntxt" => "查看详情",
                       ];
                       $usersArr = array_column($model['users'], "user_id");
                       WeworkSendMsg::send($usersArr, [], [], $params);
                       ExaminationPaper::updateAll(["is_an_hour" => 1], ["id" => $model['id']]);
                   }
               }
            }

            $startModel = ExaminationPaper::find()->with('users')->where(['status' => ExaminationPaper::STATUS_NOT_START])->asArray();
            foreach ($startModel->batch(1000) as $starts) {
                foreach ($starts as $start) {
                    $titleStr = "";
                    $flag = 0;
                    if($start['start_time'] - strtotime(date("Y-m-d H:i:00")) <= 600 && $start['is_ten'] == 0) {
                        $titleStr = "您有考试即将开始，请及时参加考试";
                        $flag = 1;
                    } elseif ($start['start_time'] - strtotime(date("Y-m-d H:i:00")) <= 1800 && $start['is_remind'] == 1 && $start['is_thirty'] == 0) {
                       $titleStr =  "您有考试即将开始，请及时参加考试";
                        $flag = 2;
                    }
                    if($titleStr) {
                        //推送消息
                        $time = date("Y-m-d H:i:s", $start['start_time']). ' - '. date("Y-m-d H:i:s", $start['end_time']);
                        $params = [
                            "title" => $titleStr,
                            "description" => "
试卷名称：{$start['name']}
考试时间：{$time}
发布人：{$start['author_name']}
",
                            "url" => \Yii::$app->params['wework']['frontPaperUrl'].$start['id'],
                            "btntxt" => "查看详情",
                        ];
                        $usersArray = array_column($start['users'], "user_id");
                        WeworkSendMsg::send($usersArray, [], [], $params);
                        if($flag == 1) {
                            ExaminationPaper::updateAll(["is_ten" => 1], ["id" => $start['id']]);
                        } else if($flag == 2) {
                            ExaminationPaper::updateAll(["is_thirty" => 1], ["id" => $start['id']]);
                        }
                    }
                }
            }
            echo "执行发送考试通知成功".PHP_EOL;
        } catch (\Exception $e) {
            echo "执行发送考试通知失败".$e->getMessage().PHP_EOL;
        }

    }


    /**
     * 文章定时发布
     */
    public function actionArticleStatus()
    {
        $trans = \Yii::$app->db->beginTransaction();
        try {
            $models = Article::find()->where(['status' => Article::STATUS_WAIT])->andWhere(["<=", 'timing_date', time()])->asArray();

            foreach ($models->batch(1000) as $items) {
                foreach ($items as $item) {
                    if(!Article::updateAll(["status" => Article::STATUS_ACTIVE], ["id" => $item['id']])) {
                        throw new Exception("修改文章定时发布状态失败");
                    }
                    //推送消息
                    if($item['is_push_msg'] == 1) {
                        try {
                            $info = $item['type'] == Article::TYPE_TYKX ? "酷讯" : "微学堂";
                            $urlType = $item['type'] == Article::TYPE_TYKX ? "message" : "school";
                            $params = [
                                "title" => "您收到一个{$info}内容",
                                "description" => "
    标题：{$item['title']}
    发布人：{$item['author_name']}
    ",
                                "url" => \Yii::$app->params['wework']['frontArticleUrl']."type={$urlType}&id={$item['id']}",
                                "btntxt" => "查看详情",
                            ];
                            $usersArr = array_column(ArticleReadObject::find()->where(["article_id" => $item['id']])->asArray()->all(), "user_id");
                            if($usersArr) {
                                WeworkSendMsg::send($usersArr, [], [], $params);
                            }
                            ArticleReadObject::updateAll(["is_receive_msg" => 1], ["article_id" => $item['id']]);
                        }catch (\Exception $er) {
                            \Yii::$app->session->setFlash("success", "企业推送消息发送失败:".$er->getMessage());
                        }
                    }
                }
            }

            $trans->commit();
            echo "执行定时发布文章任务成功".PHP_EOL;
        } catch (\Exception $e) {
            $trans->rollBack();
            \Yii::error("执行定时发布文章任务失败：".$e->getMessage().$e->getTraceAsString());
            echo "执行定时发布文章任务失败".$e->getMessage().PHP_EOL;
        }

    }
}
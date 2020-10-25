<?php

namespace backend\controllers;

use backend\models\ExaminationUsersSearch;
use common\components\wework\WeworkSendMsg;
use common\helpers\StringHelper;
use common\models\ExaminationAnswer;
use common\models\ExaminationPaperItem;
use common\models\ExaminationUsers;
use common\models\QuestionBank;
use common\models\QuestionBankItem;
use common\models\User;
use common\models\WeworkUsers;
use Yii;
use common\models\ExaminationPaper;
use backend\models\ExaminationPaperSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * ExaminationPaperController implements the CRUD actions for ExaminationPaper model.
 */
class ExaminationPaperController extends Controller
{
    public $enableCsrfValidation = false;
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all ExaminationPaper models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ExaminationPaperSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ExaminationPaper model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id, $status = 1)
    {
        $model = $this->findModel($id);

        $searchModel = new ExaminationUsersSearch();
        $searchModel->is_join = $status;
        $searchModel->paper_id = $id;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if($status == 1) {
            $page = "view";
        } else {
            $page = "not-view";
        }

        return $this->render($page, [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'status' => $status,
            'id' => $id
        ]);

    }


    /**
     * Deletes an existing ExaminationPaper model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        ExaminationPaperItem::deleteAll(['paper_id' => $id]);
        ExaminationUsers::deleteAll(['paper_id' => $id]);
        ExaminationAnswer::deleteAll(['paper_id' => $id]);

        return $this->redirect(['index']);
    }

    /**
     * Finds the ExaminationPaper model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ExaminationPaper the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ExaminationPaper::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * 批量删除
     * @param $ids
     * @param $type
     * @return \yii\web\Response
     */
    public function actionBatchDelete($ids)
    {
        $trans = Yii::$app->db->beginTransaction();
        try {
            $ids = explode(",", $ids);
            ExaminationPaper::deleteAll(['id' => $ids]);
            ExaminationPaperItem::deleteAll(['paper_id' => $ids]);
            ExaminationUsers::deleteAll(['paper_id' => $ids]);
            ExaminationAnswer::deleteAll(['paper_id' => $ids]);
            $trans->commit();
            \Yii::$app->session->setFlash("success", "操作成功");
        } catch (\Exception $e) {
            $trans->rollBack();
            \Yii::$app->session->setFlash("error", "操作失败:" . $e->getMessage());
        }
        return $this->redirect(['index']);
    }

    /**
     * @return string
     */
    public function actionCreate($type='create', $id=null)
    {
        return $this->render('create', [
            'type' => $type,
            'id' => $id,
        ]);
    }

    /**
     * @return string
     */
    public function actionStep2($type, $id)
    {
        return $this->render('step2', [
            'type' => $type,
            'id' => $id,
        ]);
    }

    /**
     * @return string
     */
    public function actionStep3($type, $id)
    {
        return $this->render('step3', [
            'type' => $type,
            'id' => $id,
        ]);
    }

    /**
     * @return string
     */
    public function actionPreview()
    {
        return $this->render('preview');
    }

    /**
     * 保存试卷
     * @param $status
     * @param $id
     * @return array
     */
    public function actionSaveData($status, $id = null)
    {
        set_time_limit(0);
        try {
            $trans = Yii::$app->db->beginTransaction();
            $body = json_decode(Yii::$app->request->rawBody, true);
            Yii::$app->response->format = Response::FORMAT_JSON;
            //试卷基本信息
            if($id) {
                $model = ExaminationPaper::findOne($id);
                if(!$model) {
                    throw new NotFoundHttpException("数据异常");
                }
                $oldStatus = $model->status;
            } else {
                $model = new ExaminationPaper();
            }
            $model->name = $body['name'];
            $model->type = $body['type'];
            $model->question_bank_ids = $body['ids'];
            $model->questions_rule = $body['type'] == 1 ? [] : $body['rule'];
            $model->is_all = $body['type'] == 1 ? $body['all'] ? 1 : 0 : 0;
            $model->start_time = strtotime(date("Y-m-d H:i:00", strtotime($body['startTime'])));
            $model->end_time = strtotime(date("Y-m-d H:i:00", strtotime($body['endTime'])));
            $model->duration_time = $body['duration'];
            $model->is_notice = $body['notice'] ? 1 : 0;
            $model->is_remind = $body['advancePush'] ? 1 : 0;
            $model->pass_mark = $body['passingScore'];
            $model->explain = $body['explain'];
            $model->status = $status;
            $model->total_grade = $body['totalScore'];
            $model->participant_num = 0;
            $model->range = $body['range'];
            if($model->status != ExaminationPaper::STATUS_DRAFT) {
                if($model->start_time <= time()) {
                    $model->status = ExaminationPaper::STATUS_GOING;
                }
                if($model->end_time <= time()) {
                    $model->status = ExaminationPaper::STATUS_END;
                }
            }
//            $object = $body['range'] == 1 ? $body['YuanRen'] : [];
//            $objArr = [];
//            foreach ($object as $k => $obj) {
//                if(in_array($obj['userid'], $objArr)) {
//                    unset($object[$k]);
//                } else {
//                    $objArr[] = $obj['userid'];
//                }
//            }

            $model->user_object = $body['range'] == 1 ? $body['YuanRen'] : [];
            $model->user_department_object = $body['range'] == 1 ? $body['YuanBu'] : [];
            if($model->isNewRecord) {
                $model->sn = StringHelper::generateSn("SJ");
                $model->created_at   = time();
                $model->author_id    = Yii::$app->user->identity->id;
                $model->author_name  = Yii::$app->user->identity->name;
            }
            $model->updated_at   = time();
            if(!$model->save()) {
                throw new \Exception("save paper fail：".current($model->getFirstErrors()));
            }

            //试题
            $itemArr = [];
            if(!$model->isNewRecord) {
                foreach ($body['selList'] as $item) {
                    if(isset($item['bank_name'])) {
                        $bank_name = $item['bank_name'];
                    } else if(isset($item['questionBank']['name'])) {
                        $bank_name = $item['questionBank']['name'];
                    } else {
                        $bank_name = "";
                    }

                    $itemArr[] = [
                        $model->primaryKey,
                        $item['bank_id'],
                        $bank_name,
                        $item['id'],
                        $item['title'],
                        $item['type'],
                        $item['grade'],
                        is_string($item['options']) && json_decode($item['options'], true) ? json_decode($item['options'], true) : $item['options'],
                        is_string($item['answer']) && json_decode($item['answer'], true) ? json_decode($item['answer'], true) : $item['answer'],
                        $item['sort'],
                        time(),
                        time()
                    ];
                }
            } else {
                foreach ($body['selList'] as $item) {
                    $itemArr[] = [
                        $model->primaryKey,
                        $item['bank_id'],
                        $item['questionBank']['name'],
                        $item['id'],
                        $item['title'],
                        $item['type'],
                        $item['grade'],
                        $item['options'],
                        $item['answer'],
                        $item['sort'],
                        time(),
                        time()
                    ];
                }
            }


            if(!$model->isNewRecord) {
                ExaminationPaperItem::deleteAll(['paper_id' => $model->id]);
            }
            \Yii::$app->db->createCommand()->batchInsert('examination_paper_item', ['paper_id', 'bank_id', 'bank_name', 'item_id', 'item_title', 'item_type', 'item_grade', 'item_option', 'item_answer', 'sort', 'created_at', 'updated_at'], $itemArr)->execute();
            //总题数
            $model->total_items = count($body['selList']);
            if(!$model->save(false)) {
                throw new \Exception("编辑总题数失败：".current($model->getFirstErrors()));
            }

            //参考人员
            WeworkUsers::batchInsert($body, $model->primaryKey);


            $t_count                    = ExaminationUsers::find()->where(['paper_id' => $model->id])->count("1");
            $model->total_num           = $t_count;
            $model->not_participant_num = $t_count;
            if (!$model->save(false)) {
                throw new \Exception("编辑参考人员总人数失败：" . current($model->getFirstErrors()));
            }

            $trans->commit();



            //发送推送消息
            if($model->status != ExaminationPaper::STATUS_DRAFT && $model->is_notice == 1) {
                try {
                    $newUserArr = [];
                    if(!$id) {
                        $title = "您收到一条考试邀请";
                        $usersArr = array_column(ExaminationUsers::find()->where(["paper_id" => $model->id])->asArray()->all(), "user_id");
                    } else {
                        if($oldStatus == ExaminationPaper::STATUS_DRAFT) {
                            if($model->status == ExaminationPaper::STATUS_GOING) {
                                $title = "您有考试已经开始了";
                            } else {
                                $title = "您收到一条考试邀请";
                            }
                        } else {
                            if($model->status == ExaminationPaper::STATUS_GOING) {
                                $title = "您有考试已经开始了";
                            } else {
                                $title = "考试变更通知";
                            }
                        }
                        $usersArr = array_column(ExaminationUsers::find()->where(["is_receive_msg" => 1, "paper_id" => $model->id])->asArray()->all(), "user_id");
                        $newUserArr = array_column(ExaminationUsers::find()->where(["is_receive_msg" => 0, "paper_id" => $model->id])->asArray()->all(), "user_id");
                    }

                    $time = date("Y-m-d H:i:s", $model->start_time). ' - '. date("Y-m-d H:i:s", $model->end_time);
                    $params = [
                        "title" => $title,
                        "description" => "
    试卷名称：{$model->name}
    考试时间：{$time}
    发布人：{$model->author_name}
    ",
                        "url" => \Yii::$app->params['wework']['frontPaperUrl'].$model->id,
                        "btntxt" => "查看详情",
                    ];

                    if($usersArr) {
                        WeworkSendMsg::send($usersArr, [], [], $params);
                    }

                    if($newUserArr) {
                        $params = [
                            "title" => "您收到一条考试邀请",
                            "description" => "
    试卷名称：{$model->name}
    考试时间：{$time}
    发布人：{$model->author_name}
    ",
                            "url" => \Yii::$app->params['wework']['frontPaperUrl'].$model->id,
                            "btntxt" => "查看详情",
                        ];

                        WeworkSendMsg::send($newUserArr, [], [], $params);
                    }

                    ExaminationUsers::updateAll(["is_receive_msg" => 1], ["paper_id" => $model->id]);
                    return [
                        "status" => 1,
                        "msg" => "操作成功"
                    ];
                }catch (\Exception $er) {
                    return [
                        "status" => 1,
                        "msg" => "操作成功,但企业推送消息发送失败:".$er->getMessage()
                    ];
                }
            } else {
                return [
                    "status" => 1,
                    "msg" => "操作成功"
                ];
            }

        } catch (\Exception $e) {
            $trans->rollBack();

            Yii::error($e->getMessage());
            return [
                "status" => -1,
                "msg" => $e->getMessage()
            ];
        }

    }

    /**
     * 查看答卷
     * @param $id
     * @return string
     */
    public function actionViewPaper($id)
    {
        $model = ExaminationUsers::find()->with([
            "paperInfo",
            "paperItems" => function($q) use ($id) {
                return $q->with(["paperAnswer" => function($q2) use ($id) {
                    return $q2->where(["examination_user_id" => $id]);
                }]);
            },
        ])
            ->where(['id' => $id])
            ->asArray()
            ->one();
        return $this->render('answer', [
            "model" => $model
        ]);
    }

    /**
     * 阅卷
     * @param $id
     * @return string
     */
    public function actionGoOver($id)
    {
        $model = ExaminationUsers::find()->with([
            "paperInfo",
            "paperItems" => function($q) use ($id) {
                return $q->with(["paperAnswer" => function($q2) use ($id) {
                    return $q2->where(["examination_user_id" => $id]);
                }]);
            },
        ])
            ->where(['id' => $id])
            ->asArray()
            ->one();
        $wait = ExaminationAnswer::find()
            ->with('paperItem')->where(['is_true' => -1, "examination_user_id" => $id])
            ->asArray()
            ->all();
        return $this->render('go-over', [
            "model" => $model,
            "wait" => $wait
        ]);
    }

    /**
     * 阅卷
     * @return array
     */
    public function actionPaperInspection()
    {
        $body = json_decode(\Yii::$app->request->rawBody, true);
        Yii::$app->response->format = Response::FORMAT_JSON;
        $trans = Yii::$app->db->beginTransaction();
        try {
            /* @var $model ExaminationAnswer */
            $model = ExaminationAnswer::find()->where(['id' => $body['id'], 'is_true' => -1])->one();
            if(!$model) {
               throw new \Exception("数据异常，题目不存在");
            }
            $model->grade = $body['grade'];
            $model->is_true = $body['is_true'];
            if(!$model->save(false)) {
                throw new \Exception(current($model->getFirstErrors()));
            }
            $model->examinationUser->grade += $body['grade'];
            $flag = false;
            if(!ExaminationAnswer::find()->where(['is_true' => -1, 'paper_id' => $model->paper_id, 'examination_user_id' => $model->examination_user_id, 'user_id' => $model->user_id])->exists()) {
                if($model->examinationUser->grade >= $model->examinationUser->paperInfo->pass_mark) {
                    $model->examinationUser->status = 1;
                } else {
                    $model->examinationUser->status = 0;
                }
                $flag = true;
            }
            if($model->is_true == 1) {
                $model->examinationUser->right_items += 1;
            }
            $model->examinationUser->save(false);
            $trans->commit();

            if($flag) {
                try {
                    $title = $model->examinationUser->status == 1 ? "您有考试通过了" : "您有考试未通过";
                    //推送消息
                    $time   = date("Y-m-d H:i:s", $model->paper->start_time) . ' - ' . date("Y-m-d H:i:s", $model->paper->end_time);
                    $params = [
                        "title" => $title,
                        "description" => "
试卷名称：{$model->paper->name}
考试时间：{$time}
发布人：{$model->paper->author_name}
",
                        "url" => \Yii::$app->params['wework']['frontPaperUrl'].$model->paper_id,
                        "btntxt" => "查看详情",
                    ];

                    WeworkSendMsg::send([$model->examinationUser->user_id], [], [], $params);
                    return [
                        "status" => 1,
                        "msg" => "操作成功"
                    ];
                } catch (\Exception $ee) {
                    return [
                        "status" => 1,
                        "msg" => "操作成功, 但推送企业微信通知失败：" . $ee->getMessage()
                    ];
                }
            } else {
                return [
                    "status" => 1,
                    "msg" => "操作成功"
                ];
            }
        } catch (\Exception $e) {
            $trans->rollBack();
            return [
                "status" => -1,
                "msg" => $e->getMessage()
            ];
        }

    }

    public function actionTask()
    {
        set_time_limit(0);
         echo "开始修复。。。";
         $trans = Yii::$app->db->beginTransaction();
         try {
             $ids = [19,21,22,23];
             $models = ExaminationAnswer::find()->where(["paper_id" => $ids])->all();
             foreach ($models as $model) {
                 $bank = ExaminationPaperItem::findOne($model->examination_item_id);
                 if(in_array($bank->item_type, [1,3])) {
                     if($bank->item_answer == $model->answer) {
                         $model->is_true = 1;
                         $model->grade = $bank->item_grade;
                     } else {
                         $model->is_true = 0;
                         $model->grade = 0;
                     }
                     if(!$model->save(false)) {
                         throw new \Exception(current($model->getFirstErrors()));
                     }
                     $model->examinationUser->grade += $model->grade;
                     if($model->is_true == 1) {
                         $model->examinationUser->right_items += 1;
                     }
                     if($model->paper_id == 23 || $model->examinationUser->id == 3346) {
                         if($model->examinationUser->grade >= $model->examinationUser->paperInfo->pass_mark) {
                             $model->examinationUser->status = 1;
                         } else {
                             $model->examinationUser->status = 0;
                         }
                     }
                     $model->examinationUser->save(false);
                 }
             }
             $trans->commit();
             echo "修复成功";
         } catch (\Exception $e) {
             $trans->rollBack();
             echo "修复失败:".$e->getMessage();
         }


    }
}

<?php

namespace backend\controllers;

use backend\models\ActivityCommentSearch;
use backend\models\ActivityLikeSearch;
use backend\models\ActivityOptionForm;
use backend\models\ActivityReadObjectSearch;
use common\components\wework\WeworkSendMsg;
use common\helpers\ModelHelper;
use common\models\ActivityComment;
use common\models\ActivityItem;
use common\models\ActivityLike;
use common\models\ActivityReadObject;
use common\models\WeworkUsers;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Yii;
use common\models\Activity;
use backend\models\ActivitySearch;
use yii\base\Model;
use yii\bootstrap\ActiveForm;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * ActivityController implements the CRUD actions for Activity model.
 */
class ActivityController extends Controller
{
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
     * Lists all Activity models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ActivitySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Activity model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
            'id' => $id,
        ]);
    }

    /**
     * 报名统计
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionStatistics($id, $status = 1)
    {
        $model = $this->findModel($id);
        if($status == 1) {
            $status = [1, -1];
        }

        $searchModel             = new ActivityReadObjectSearch();
        $searchModel->activity_id = $id;
        $searchModel->apply_status = $status;
        $dataProvider            = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('statistics', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'status' => is_array($status) ? 1 : 0,
            'id' => $id,
        ]);
    }

    /**
     * Creates a new Activity model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model     = new Activity();
        $modelAttr = [new ActivityItem()];
        $modelOptionAttr = [new ActivityOptionForm()];
        $model->loadDefaultValues();
        $model->limit_person_num = 0;
        $model->start_time = '';
        $model->end_time = '';
        $model->close_date = '';
        foreach ($modelAttr as $attr) {
            $attr->loadDefaultValues();
        }
        if ($model->load(Yii::$app->request->post())) {
            $trans = Yii::$app->db->beginTransaction();
            try {
                $modelAttr = ModelHelper::createMultiple(ActivityItem::class);
                $modelOptionAttr = ModelHelper::createMultiple(ActivityOptionForm::class);
                //填充模型
                Model::loadMultiple($modelAttr, Yii::$app->request->post());
                Model::loadMultiple($modelOptionAttr, Yii::$app->request->post());

                $model->author_id   = Yii::$app->user->identity->id;
                $model->author_name = Yii::$app->user->identity->name;
                $model->start_time = strtotime(date("Y-m-d H:i:00", strtotime( $model->start_time)));
                $model->end_time = strtotime(date("Y-m-d H:i:00", strtotime( $model->end_time)));
                $model->close_date = strtotime( $model->close_date);
                $model->created_at = time();
                $model->updated_at = time();
                $model->read_object = $model->range == 1 ? $model->read_object : [];
                $model->user_department_object = $model->range == 1 && $model->user_department_object ? json_decode($model->user_department_object) : [];
                if(!$model->limit_person_num) {
                    $model->limit_person_num = 0;
                }
                if($model->status != Activity::STATUS_DRAFT) {
                    if($model->start_time <= time()) {
                        $model->status = Activity::STATUS_GOING;
                    }
                    if($model->end_time <= time()) {
                        $model->status = Activity::STATUS_END;
                    }
                }

                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return ArrayHelper::merge(
                        ActiveForm::validateMultiple($modelAttr),
                        ActiveForm::validate($model),
                        ActiveForm::validateMultiple($modelOptionAttr)
                    );
                }

                if (!$model->save()) {
                    throw new Exception(current($model->getFirstErrors()));
                }

                foreach ($modelAttr as $k => $attr) {
                    $options = Yii::$app->request->post("ActivityOptionForm");
                    if($attr->item_type != 3) {
                        $attr->item_options = $options[$k]['name'];
                    } else {
                        $attr->item_options = [];
                    }
                    $attr->activity_id = $model->getPrimaryKey();
                    if (!$attr->save(false)) {
                        throw new Exception('activity item save fail');
                        break;
                    }
                }

                //人员
                WeworkUsers::batchInsertByActivity($model, $model->primaryKey);
                if($model->range == 0) {
                    //计算总人数
                    $model->total_num = WeworkUsers::find()->count("1");
                    if(!$model->save(false)) {
                        throw new \Exception("编辑人员总人数失败：".current($model->getFirstErrors()));
                    }
                }

                $trans->commit();
                //发送推送消息
                if($model->status != Activity::STATUS_DRAFT && $model->is_push_msg == 1) {
                    try {
                        $time = date("Y-m-d H:i:s", $model->start_time). ' - '. date("Y-m-d H:i:s", $model->end_time);
                        $params = [
                            "title" => "您收到一条活动邀请",
                            "description" => "
    标题：{$model->title}
    活动时间：{$time}
    发布人：{$model->author_name}
    ",
                            "url" => \Yii::$app->params['wework']['frontActivityUrl'].$model->id,
                            "btntxt" => "查看详情",
                        ];
                        $usersArr = array_column(ActivityReadObject::find()->where(["activity_id" => $model->id])->asArray()->all(), "user_id");
                        if($usersArr) {
                            WeworkSendMsg::send($usersArr, [], [], $params);
                        }
                        ActivityReadObject::updateAll(["is_receive_msg" => 1], ["activity_id" => $model->id]);
                        \Yii::$app->session->setFlash("success", "操作成功");
                    }catch (\Exception $er) {
                        \Yii::$app->session->setFlash("success", "操作成功,但企业推送消息发送失败:".$er->getMessage());
                    }
                } else {
                    \Yii::$app->session->setFlash("success", "操作成功");
                }
            } catch (\Exception $e) {
                $trans->rollBack();
                \Yii::$app->session->setFlash("error", "操作失败:" . $e->getMessage());
            }
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
            'modelAttr' => $modelAttr,
            'modelOptionAttr' => $modelOptionAttr,
        ]);
    }

    /**
     * Updates an existing Activity model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $oldStatus = $model->status;
        $model->start_time = date("Y-m-d H:i:s", $model->start_time);
        $model->end_time = date("Y-m-d H:i:s", $model->end_time);
        $model->close_date = date("Y-m-d H:i:s", $model->close_date);
        $model->read_object = is_string($model->read_object) ? json_encode(json_decode($model->read_object)) : json_encode($model->read_object);
        $model->user_department_object = json_encode($model->user_department_object);
        $modelAttr = $model->items;
        $modelOptionAttr = [];

        foreach ($modelAttr as $k =>$item) {
            if($item->item_type != 3) {
                foreach ($item->item_options as $op) {
                    $attr = new ActivityOptionForm();
                    $attr->name = $op;
                    $modelOptionAttr[] = $attr;
                }
            }
        }

        if ($model->load(Yii::$app->request->post())) {
            $oldIDs = ArrayHelper::map($modelAttr, 'id', 'id');
            $modelAttr = ModelHelper::createMultiple(ActivityItem::class, $modelAttr);
            $modelOptionAttr = ModelHelper::createMultiple(ActivityOptionForm::class);

            Model::loadMultiple($modelAttr, Yii::$app->request->post());
            Model::loadMultiple($modelOptionAttr, Yii::$app->request->post());

            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelAttr, 'id', 'id')));
            $model->start_time = strtotime(date("Y-m-d H:i:00", strtotime( $model->start_time)));
            $model->end_time = strtotime(date("Y-m-d H:i:00", strtotime( $model->end_time)));
            $model->close_date = strtotime( $model->close_date);
            $model->updated_at = time();
            $model->read_object = $model->range == 1 ? $model->read_object : [];
            $model->user_department_object = $model->range == 1 && $model->user_department_object ? json_decode($model->user_department_object) : [];

            if(!$model->limit_person_num) {
                $model->limit_person_num = 0;
            }
            if($model->status != Activity::STATUS_DRAFT) {
                if($model->start_time <= time()) {
                    $model->status = Activity::STATUS_GOING;
                }
                if($model->end_time <= time()) {
                    $model->status = Activity::STATUS_END;
                }
            }
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ArrayHelper::merge(
                    ActiveForm::validateMultiple($modelAttr),
                    ActiveForm::validate($model),
                    ActiveForm::validateMultiple($modelOptionAttr)
                );
            }
            $trans = \Yii::$app->db->beginTransaction();
            try {
                //人员
//                ActivityReadObject::deleteAll(['activity_id' => $model->id]);
                WeworkUsers::batchInsertByActivity($model, $model->id);
                if($model->range == 0) {
                    //计算总人数
                    $model->total_num = WeworkUsers::find()->count("1");
                }

                if (!$model->save(false)){
                    throw new Exception('model save fail：'.current($model->getFirstErrors()));
                }

                if (!empty($deletedIDs)) {
                    ActivityItem::deleteAll(['id' => $deletedIDs]);
                }

                foreach ($modelAttr as $k => $attr) {
                    $options = Yii::$app->request->post("ActivityOptionForm");
                    if($attr->item_type != 3) {
                        $res = [];
                        foreach ($options[$k]['name'] as $key => &$o) {
                            if($o) {
                                $res[] = $o;
                            }
                        }
                        $attr->item_options = $res;
                    } else {
                        $attr->item_options = [];
                    }

                    $attr->activity_id = $model->getPrimaryKey();
                    if (!$attr->save(false)) {
                        throw new Exception('activity item save fail');
                        break;
                    }
                }
                $trans->commit();
                //发送推送消息
                if($model->status != Activity::STATUS_DRAFT) {
                    try {
                        $newUserArr = [];
                        $time = date("Y-m-d H:i:s", $model->start_time). ' - '. date("Y-m-d H:i:s", $model->end_time);
                        if(!($oldStatus == Activity::STATUS_DRAFT && $model->is_push_msg == 0)) {
                            if($oldStatus == Activity::STATUS_DRAFT ) {
                                if($model->status == Activity::STATUS_GOING) {
                                    $title = "活动已经开始了";
                                } else {
                                    $title = "您收到一条活动邀请";
                                }
                            } else {
                                if($model->status == Activity::STATUS_GOING) {
                                    $title = "活动已经开始了";
                                } else {
                                    $title = "活动变更通知";
                                }
                            }
                            $params = [
                                "title" => "{$title}",
                                "description" => "
    标题：{$model->title}
    活动时间：{$time}
    发布人：{$model->author_name}
    ",
                                "url" => \Yii::$app->params['wework']['frontActivityUrl'].$model->id,
                                "btntxt" => "查看详情",
                            ];
                            if($oldStatus == Activity::STATUS_DRAFT && $model->is_push_msg == 1) {
                                $usersArr = array_column(ActivityReadObject::find()->where(["activity_id" => $model->id])->asArray()->all(), "user_id");
                            } else {
                                $usersArr = array_column(ActivityReadObject::find()->where(["is_receive_msg" => 1, "activity_id" => $model->id])->asArray()->all(), "user_id");
                                $newUserArr = array_column(ActivityReadObject::find()->where(["is_receive_msg" => 0, "activity_id" => $model->id])->asArray()->all(), "user_id");
                            }
                            if($usersArr) {
                                WeworkSendMsg::send($usersArr, [], [], $params);
                            }
                            if($newUserArr) {
                                $params = [
                                    "title" => "您收到一条活动邀请",
                                    "description" => "
    标题：{$model->title}
    活动时间：{$time}
    发布人：{$model->author_name}
    ",
                                    "url" => \Yii::$app->params['wework']['frontActivityUrl'].$model->id,
                                    "btntxt" => "查看详情",
                                ];

                                WeworkSendMsg::send($newUserArr, [], [], $params);
                            }
                            ActivityReadObject::updateAll(["is_receive_msg" => 1], ["activity_id" => $model->id]);
                            \Yii::$app->session->setFlash("success", "操作成功");
                        } else {
                            \Yii::$app->session->setFlash("success", "操作成功");
                        }

                    }catch (\Exception $er) {
                        \Yii::$app->session->setFlash("success", "操作成功,但企业推送消息发送失败:".$er->getMessage());
                    }
                } else {
                    \Yii::$app->session->setFlash("success", "操作成功");
                }
            } catch (\Exception $e) {
                $trans->rollBack();
                \Yii::$app->session->setFlash("error", "操作失败:" . $e->getMessage());
            }
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
                'modelAttr' => (empty($modelAttr)) ? [new ActivityItem()] : $modelAttr,
                'modelOptionAttr' => (empty($modelOptionAttr)) ? [new ActivityOptionForm()] : $modelOptionAttr,
            ]);
        }
    }

    /**
     * Deletes an existing Activity model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        ActivityReadObject::deleteAll(['activity_id' => $id]);
        ActivityItem::deleteAll(['activity_id' => $id]);
        ActivityComment::deleteAll(['activity_id' => $id]);
        ActivityLike::deleteAll(['activity_id' => $id]);
        return $this->redirect(['index']);
    }

    /**
     * Finds the Activity model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Activity the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Activity::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested Activity does not exist.');
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
            Activity::deleteAll(['id' => $ids]);
            ActivityReadObject::deleteAll(['activity_id' => $ids]);
            ActivityItem::deleteAll(['activity_id' => $ids]);
            ActivityComment::deleteAll(['activity_id' => $ids]);
            ActivityLike::deleteAll(['activity_id' => $ids]);

            $trans->commit();
            \Yii::$app->session->setFlash("success", "操作成功");
        } catch (\Exception $e) {
            $trans->rollBack();
            \Yii::$app->session->setFlash("error", "操作失败:" . $e->getMessage());
        }
        return $this->redirect(['index']);
    }

    /**
     * 阅读对象信息
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionReadInfo($id, $status = 1)
    {
        $searchModel             = new ActivityReadObjectSearch();
        $searchModel->activity_id = $id;
        $searchModel->is_read    = $status;
        $dataProvider            = $searchModel->search(Yii::$app->request->queryParams);

        $endCount = ActivityReadObject::find()->where(['activity_id' => $id, 'is_read' => 1])->count(1);
        $count = ActivityReadObject::find()->where(['activity_id' => $id, 'is_read' => 0])->count(1);
        return $this->render('read', [
            'dataProvider' => $dataProvider,
            'endCount' => $endCount,
            'count' => $count,
            'status' => $status,
            'id' => $id,
        ]);
    }

    /**
     * 导出全部
     * @param $status
     * @param $id
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function actionExportAll($status, $id)
    {
        set_time_limit(0);
        $filename         = "阅读情况";
        $query            = ActivityReadObject::find()->where(['is_read' => $status, 'activity_id' => $id]);
        $response         = \Yii::$app->response;
        $response->format = Response::FORMAT_RAW;
        $response->headers->add('Content-Type', 'application/xlsx');

        $ext   = '.xlsx';
        $title = ["姓名", "部门", "职位", "手机", "阅读时间"];

        $az          = range('A', 'Z');
        $attrNum     = count($title) - 1;
        $index       = range('A', $az[$attrNum]);
        $spreadsheet = new Spreadsheet();
        $spreadsheet->setActiveSheetIndex(0);

        foreach ($title as $key => $row) {
            $attrs[]   = $row;
            $cellIndex = $index[$key] . '1';
            //列宽
            $spreadsheet->getActiveSheet()->getColumnDimension($index[$key])->setWidth(30);
            //设置字体大小
            $spreadsheet->getActiveSheet()->getStyle($cellIndex)->getFont()->setName('宋体')->setSize(14);
            //设置文本格式
            $spreadsheet->getActiveSheet()->setCellValueExplicit($cellIndex, $row, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $spreadsheet->getActiveSheet()->getStyle($cellIndex)->getNumberFormat()->setFormatCode("@");
        }

        foreach ($query->each(200) as $key => $query_item) {
            foreach ($title as $akey => $item) {
                if ($item == "姓名") {
                    $value = $query_item->username;
                } elseif ($item == "部门") {
                    $value = implode(",", array_column($query_item->department_name, 'name'));
                } elseif ($item == "职位") {
                    $value = $query_item->position;
                } elseif ($item == "手机") {
                    $value = $query_item->phone;
                } else {
                    $value = $query_item->read_time ? date("Y-m-d H:i:s", $query_item->read_time) : '';
                }
                $i         = $key + 2;
                $cellIndex = $index[$akey] . $i;
                //设置字体大小
                $spreadsheet->getActiveSheet()->getStyle($cellIndex)->getFont()->setName('宋体')->setSize(14);
                $spreadsheet->getActiveSheet()->getStyle($cellIndex)->getAlignment()->setWrapText(true);
                $spreadsheet->getActiveSheet()->setCellValue($cellIndex, $value);
            }
        }

        if ($ext == 'csv') {
            $writer = IOFactory::createWriter($spreadsheet, 'Csv')->setDelimiter(',')->setEnclosure('"')->setUseBOM(true);
        } elseif ($ext == 'xls') {
            $writer = IOFactory::createWriter($spreadsheet, 'Xls');
            $mime   = 'application/vnd.ms-excel';
        } else {
            $mime   = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        }
        \Yii::$app->response->setDownloadHeaders($filename . $ext, $mime)->send();
        $writer->save("php://output");
    }

    /**
     * 导出excel
     * @param $ids
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function actionExport($ids)
    {
        $ids = explode(",", $ids);
        set_time_limit(0);
        $filename         = "阅读情况";
        $query            = ActivityReadObject::find()->where(['id' => $ids]);
        $response         = \Yii::$app->response;
        $response->format = Response::FORMAT_RAW;
        $response->headers->add('Content-Type', 'application/xlsx');

        $ext   = '.xlsx';
        $title = ["姓名", "部门", "职位", "手机", "阅读时间"];

        $az          = range('A', 'Z');
        $attrNum     = count($title) - 1;
        $index       = range('A', $az[$attrNum]);
        $spreadsheet = new Spreadsheet();
        $spreadsheet->setActiveSheetIndex(0);

        foreach ($title as $key => $row) {
            $attrs[]   = $row;
            $cellIndex = $index[$key] . '1';
            //列宽
            $spreadsheet->getActiveSheet()->getColumnDimension($index[$key])->setWidth(30);
            //设置字体大小
            $spreadsheet->getActiveSheet()->getStyle($cellIndex)->getFont()->setName('宋体')->setSize(14);
            //设置文本格式
            $spreadsheet->getActiveSheet()->setCellValueExplicit($cellIndex, $row, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $spreadsheet->getActiveSheet()->getStyle($cellIndex)->getNumberFormat()->setFormatCode("@");
        }

        foreach ($query->each(200) as $key => $query_item) {
            foreach ($title as $akey => $item) {
                if ($item == "姓名") {
                    $value = $query_item->username;
                } elseif ($item == "部门") {
                    $value = implode(",", array_column($query_item->department_name, 'name'));
                } elseif ($item == "职位") {
                    $value = $query_item->position;
                } elseif ($item == "手机") {
                    $value = $query_item->phone;
                } else {
                    $value = $query_item->read_time ? date("Y-m-d H:i:s", $query_item->read_time) : '';
                }
                $i         = $key + 2;
                $cellIndex = $index[$akey] . $i;
                //设置字体大小
                $spreadsheet->getActiveSheet()->getStyle($cellIndex)->getFont()->setName('宋体')->setSize(14);
                $spreadsheet->getActiveSheet()->getStyle($cellIndex)->getAlignment()->setWrapText(true);
                $spreadsheet->getActiveSheet()->setCellValue($cellIndex, $value);
            }
        }

        if ($ext == 'csv') {
            $writer = IOFactory::createWriter($spreadsheet, 'Csv')->setDelimiter(',')->setEnclosure('"')->setUseBOM(true);
        } elseif ($ext == 'xls') {
            $writer = IOFactory::createWriter($spreadsheet, 'Xls');
            $mime   = 'application/vnd.ms-excel';
        } else {
            $mime   = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        }
        \Yii::$app->response->setDownloadHeaders($filename . $ext, $mime)->send();
        $writer->save("php://output");
    }

    /**
     * 评论信息
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionCommentInfo($id)
    {
        $model                   = $this->findModel($id);
        $searchModel             = new ActivityCommentSearch();
        $searchModel->activity_id = $id;
        $dataProvider            = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('comment', [
            'dataProvider' => $dataProvider,
            'id' => $model->id
        ]);
    }

    /**
     * 批量删除评论
     * @param $ids
     * @throws Exception
     */
    public function actionBatchDeleteComment($ids, $id)
    {
        $trans = Yii::$app->db->beginTransaction();
        try {
            $ids = explode(",", $ids);
            ActivityComment::updateAll(["del" => 0],['id' => $ids]);
            $trans->commit();
            \Yii::$app->session->setFlash("success", "操作成功");
        } catch (\Exception $e) {
            $trans->rollBack();
            \Yii::$app->session->setFlash("error", "操作失败:" . $e->getMessage());
        }
        return $this->redirect(['comment-info', 'id' => $id]);
    }

    /**
     * 点赞信息
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionLikeInfo($id)
    {
        $searchModel             = new ActivityLikeSearch();
        $searchModel->activity_id = $id;
        $dataProvider            = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('like', [
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * 导出活动人员信息(全部)
     * @param $id
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function actionBatchExportApplyInfoAll($id, $status)
    {
        if($status != 0 ) {
            $status = [1, -1];
        }
        set_time_limit(0);
        $filename         = "报名统计";
        $query            = ActivityReadObject::find()->where(['activity_id' => $id, 'apply_status' => $status]);
        $response         = \Yii::$app->response;
        $response->format = Response::FORMAT_RAW;
        $response->headers->add('Content-Type', 'application/xlsx');

        $ext   = '.xlsx';
        if($status != 0) {
            $title = ["报名人", "状态", "部门名称", "岗位", "手机号码", "报名时间"];
            $titleArr = array_column(ActivityItem::find()->where(['activity_id' => $id])->asArray()->all(), 'item_title');
            $title = array_merge($title, $titleArr);
        } else {
            $title = ["报名人", "部门名称", "岗位", "手机号码"];
        }


        $az          = range('A', 'Z');
        $attrNum     = count($title) - 1;
        $index       = range('A', $az[$attrNum]);
        $spreadsheet = new Spreadsheet();
        $spreadsheet->setActiveSheetIndex(0);

        foreach ($title as $key => $row) {
            $attrs[]   = $row;
            $cellIndex = $index[$key] . '1';
            //列宽
            $spreadsheet->getActiveSheet()->getColumnDimension($index[$key])->setWidth(30);
            //设置字体大小
            $spreadsheet->getActiveSheet()->getStyle($cellIndex)->getFont()->setName('宋体')->setSize(14);
            //设置文本格式
            $spreadsheet->getActiveSheet()->setCellValueExplicit($cellIndex, $row, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $spreadsheet->getActiveSheet()->getStyle($cellIndex)->getNumberFormat()->setFormatCode("@");
        }

        foreach ($query->each(200) as $key => $query_item) {
            foreach ($title as $akey => $item) {
                if($status != 0) {
                    if ($item == "报名人") {
                        $value = $query_item->username;
                    } elseif ($item == "状态") {
                        $value = ActivityReadObject::$applyStatus[$query_item->apply_status];
                    } elseif ($item == "部门名称") {
                        $value = implode(",", array_column($query_item->department_name, 'name'));
                    } elseif ($item == "岗位") {
                        $value = $query_item->position;
                    } elseif ($item == "手机号码") {
                        $value = $query_item->phone;
                    } elseif ($item == "报名时间") {
                        $value = $query_item->apply_at ? date("Y-m-d H:i:s", $query_item->apply_at) : '';
                    } else {
                        $value = "";
                        if($query_item->apply_options) {
                            foreach ($query_item->apply_options as $optionInfo) {
                                if($item == $optionInfo['item_title']) {
                                    $value = is_array($optionInfo['item_options']) ? implode(",", $optionInfo['item_options']) : $optionInfo['item_options'];
                                    break;
                                }
                            }
                        }
                    }
                } else {
                    $title = ["报名人", "部门名称", "岗位", "手机号码"];
                    if ($item == "报名人") {
                        $value = $query_item->username;
                    } elseif ($item == "部门名称") {
                        $value = implode(",", array_column($query_item->department_name, 'name'));
                    } elseif ($item == "岗位") {
                        $value = $query_item->position;
                    } else {
                        $value = $query_item->phone;
                    }
                }
                $i         = $key + 2;
                $cellIndex = $index[$akey] . $i;
                //设置字体大小
                $spreadsheet->getActiveSheet()->getStyle($cellIndex)->getFont()->setName('宋体')->setSize(14);
                $spreadsheet->getActiveSheet()->getStyle($cellIndex)->getAlignment()->setWrapText(true);
                $spreadsheet->getActiveSheet()->setCellValue($cellIndex, $value);
            }
        }

        if ($ext == 'csv') {
            $writer = IOFactory::createWriter($spreadsheet, 'Csv')->setDelimiter(',')->setEnclosure('"')->setUseBOM(true);
        } elseif ($ext == 'xls') {
            $writer = IOFactory::createWriter($spreadsheet, 'Xls');
            $mime   = 'application/vnd.ms-excel';
        } else {
            $mime   = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        }
        \Yii::$app->response->setDownloadHeaders($filename . $ext, $mime)->send();
        $writer->save("php://output");
    }

    /**
     * 导出活动人员信息
     * @param $ids
     * @param $status
     * @param $id
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function actionBatchExportApplyInfo($ids, $id, $status)
    {
        $ids = explode(",", $ids);
        set_time_limit(0);
        $filename         = "报名统计";
        $query            = ActivityReadObject::find()->where(['id' => $ids, 'activity_id' => $id]);
        $response         = \Yii::$app->response;
        $response->format = Response::FORMAT_RAW;
        $response->headers->add('Content-Type', 'application/xlsx');

        $ext   = '.xlsx';
        if($status != 0) {
            $title = ["报名人", "状态", "部门名称", "岗位", "手机号码", "报名时间"];
            $titleArr = array_column(ActivityItem::find()->where(['activity_id' => $id])->asArray()->all(), 'item_title');
            $title = array_merge($title, $titleArr);
        } else {
            $title = ["报名人", "部门名称", "岗位", "手机号码"];
        }


        $az          = range('A', 'Z');
        $attrNum     = count($title) - 1;
        $index       = range('A', $az[$attrNum]);
        $spreadsheet = new Spreadsheet();
        $spreadsheet->setActiveSheetIndex(0);

        foreach ($title as $key => $row) {
            $attrs[]   = $row;
            $cellIndex = $index[$key] . '1';
            //列宽
            $spreadsheet->getActiveSheet()->getColumnDimension($index[$key])->setWidth(30);
            //设置字体大小
            $spreadsheet->getActiveSheet()->getStyle($cellIndex)->getFont()->setName('宋体')->setSize(14);
            //设置文本格式
            $spreadsheet->getActiveSheet()->setCellValueExplicit($cellIndex, $row, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $spreadsheet->getActiveSheet()->getStyle($cellIndex)->getNumberFormat()->setFormatCode("@");
        }

        foreach ($query->each(200) as $key => $query_item) {
            foreach ($title as $akey => $item) {
                if($status != 0) {
                    if ($item == "报名人") {
                        $value = $query_item->username;
                    } elseif ($item == "状态") {
                        $value = ActivityReadObject::$applyStatus[$query_item->apply_status];
                    } elseif ($item == "部门名称") {
                        $value = implode(",", array_column($query_item->department_name, 'name'));
                    } elseif ($item == "岗位") {
                        $value = $query_item->position;
                    } elseif ($item == "手机号码") {
                        $value = $query_item->phone;
                    } elseif ($item == "报名时间") {
                        $value = $query_item->apply_at ? date("Y-m-d H:i:s", $query_item->apply_at) : '';
                    } else {
                        $value = "";
                        if($query_item->apply_options) {
                            foreach ($query_item->apply_options as $optionInfo) {
                                if($item == $optionInfo['item_title']) {
                                    $value = is_array($optionInfo['item_options']) ? implode(",", $optionInfo['item_options']) : $optionInfo['item_options'];
                                    break;
                                }
                            }
                        }
                    }
                } else {
                    $title = ["报名人", "部门名称", "岗位", "手机号码"];
                    if ($item == "报名人") {
                        $value = $query_item->username;
                    } elseif ($item == "部门名称") {
                        $value = implode(",", array_column($query_item->department_name, 'name'));
                    } elseif ($item == "岗位") {
                        $value = $query_item->position;
                    } else {
                        $value = $query_item->phone;
                    }
                }

                $i         = $key + 2;
                $cellIndex = $index[$akey] . $i;
                //设置字体大小
                $spreadsheet->getActiveSheet()->getStyle($cellIndex)->getFont()->setName('宋体')->setSize(14);
                $spreadsheet->getActiveSheet()->getStyle($cellIndex)->getAlignment()->setWrapText(true);
                $spreadsheet->getActiveSheet()->setCellValue($cellIndex, $value);
            }
        }

        if ($ext == 'csv') {
            $writer = IOFactory::createWriter($spreadsheet, 'Csv')->setDelimiter(',')->setEnclosure('"')->setUseBOM(true);
        } elseif ($ext == 'xls') {
            $writer = IOFactory::createWriter($spreadsheet, 'Xls');
            $mime   = 'application/vnd.ms-excel';
        } else {
            $mime   = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        }
        \Yii::$app->response->setDownloadHeaders($filename . $ext, $mime)->send();
        $writer->save("php://output");
    }

    /**
     * 删除评论
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionCommentDelete($id)
    {
        $model = ActivityComment::findOne($id);
        if($model) {
            $model->del = 0;
            $model->save(false);
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        return $this->redirect(['comment-info', 'id' => $model->activity_id]);
    }

    /**
     * 消息提醒
     * @param $id
     * @return array
     */
    public function actionRemind($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $trans = Yii::$app->db->beginTransaction();
        try {
            $model = ActivityReadObject::findOne($id);
            if(!$model) {
                throw new \Exception("数据异常");
            }
            $model->is_receive_msg = 1;
            $model->save(false);
            $time = date("Y-m-d H:i:s", $model->activity->start_time). ' - '. date("Y-m-d H:i:s", $model->activity->end_time);
            $params = [
                "title" => "您收到一个活动报名邀请",
                "description" => "
标题：{$model->activity->title}
活动时间：{$time}
发布人：{$model->activity->author_name}
",
                "url" => \Yii::$app->params['wework']['frontActivityUrl'].$model->activity_id,
                "btntxt" => "查看详情",
            ];
            WeworkSendMsg::send([$model->user_id], [], [], $params);
            $trans->commit();
            return [
                "msg" => "操作成功"
            ];
        } catch (\Exception $e) {
            $trans->rollBack();
            return [
                "msg" => $e->getMessage()
            ];
        }
    }
}

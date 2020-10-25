<?php

namespace backend\controllers;

use common\components\wework\WeworkSendMsg;
use common\helpers\ModelHelper;
use common\models\VoteOption;
use common\models\VoteRecord;
use common\models\VoteUserObject;
use common\models\WeworkUsers;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Yii;
use common\models\Vote;
use backend\models\VoteSearch;
use yii\base\Model;
use yii\bootstrap\ActiveForm;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * VoteController implements the CRUD actions for Vote model.
 */
class VoteController extends Controller
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
     * Lists all Vote models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new VoteSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Vote model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $count = VoteRecord::find()->where(["vote_id" => $id])->groupBy("user_id")->count();
        return $this->render('view', [
            'model' => $model,
            'count' => $count,
        ]);
    }

    /**
     * Creates a new Vote model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model     = new Vote();
        $modelAttr = [new VoteOption()];
        $model->loadDefaultValues();
        if ($model->load(Yii::$app->request->post())) {
            $trans = Yii::$app->db->beginTransaction();
            try {
                $modelAttr = ModelHelper::createMultiple(VoteOption::class);
                //填充模型
                Model::loadMultiple($modelAttr, Yii::$app->request->post());
                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return ArrayHelper::merge(
                        ActiveForm::validateMultiple($modelAttr),
                        ActiveForm::validate($model)
                    );
                }
                $options = Yii::$app->request->post("VoteOption");
                $model->options = $options;
                $model->author_id   = Yii::$app->user->identity->id;
                $model->author_name = Yii::$app->user->identity->name;
                $model->start_time = strtotime(date("Y-m-d H:i:00", strtotime( $model->start_time)));
                $model->end_time = strtotime(date("Y-m-d H:i:00", strtotime( $model->end_time)));
                $model->created_at = time();
                $model->updated_at = time();
                $model->read_object = $model->range == 1 ? $model->read_object : [];
                $model->user_department_object = $model->range == 1 && $model->user_department_object ? json_decode($model->user_department_object) : [];
                if($model->status != Vote::STATUS_DRAFT) {
                    if($model->start_time <= time()) {
                        $model->status = Vote::STATUS_GOING;
                    }
                    if($model->end_time <= time()) {
                        $model->status = Vote::STATUS_END;
                    }
                }
                if (!$model->save()) {
                    throw new Exception(current($model->getFirstErrors()));
                }

                foreach ($modelAttr as $attr) {
                    $attr->vote_id = $model->getPrimaryKey();
                    if($attr->option_image) {
                        $attr->option_image = [$attr->option_image];
                    }
                    if (!$attr->save(false)) {
                        throw new Exception('vote option save fail');
                        break;
                    }
                }

                //人员
                WeworkUsers::batchInsertByVote($model, $model->primaryKey);

                if($model->range == 0) {
                    //计算总人数
                    $model->total_num = WeworkUsers::find()->count("1");
                    if(!$model->save(false)) {
                        throw new \Exception("编辑人员总人数失败：".current($model->getFirstErrors()));
                    }
                }

                $trans->commit();
                //发送推送消息
                if($model->status != Vote::STATUS_DRAFT && $model->is_notice == 1) {
                    try {
                        $time = date("Y-m-d H:i:s", $model->start_time). ' - '. date("Y-m-d H:i:s", $model->end_time);
                        $params = [
                            "title" => "您收到一条投票邀请",
                            "description" => "
    标题：{$model->title}
    投票时间：{$time}
    发布人：{$model->author_name}
    ",
                            "url" => \Yii::$app->params['wework']['frontVoteUrl'].$model->id,
                            "btntxt" => "查看详情",
                        ];
                        $usersArr = array_column(VoteUserObject::find()->where(["vote_id" => $model->id])->asArray()->all(), "user_id");
                        if($usersArr) {
                            WeworkSendMsg::send($usersArr, [], [], $params);
                        }
                        VoteUserObject::updateAll(["is_receive_msg" => 1], ["vote_id" => $model->id]);
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
        ]);
    }

    /**
     * Updates an existing Vote model.
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
        $model->read_object = is_string($model->read_object) ? json_encode(json_decode($model->read_object)) : json_encode($model->read_object);
        $model->user_department_object = json_encode($model->user_department_object);
        $modelAttr = $model->voteOptions;
        foreach ($modelAttr as $attr) {
            if($attr->option_image) {
                $attr->option_image = $attr->option_image[0];
            }
        }
        if ($model->load(Yii::$app->request->post())) {
            $oldIDs = ArrayHelper::map($modelAttr, 'id', 'id');
            $modelAttr = ModelHelper::createMultiple(VoteOption::class, $modelAttr);
            Model::loadMultiple($modelAttr, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelAttr, 'id', 'id')));
            // ajax validation
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ArrayHelper::merge(
                    ActiveForm::validateMultiple($modelAttr),
                    ActiveForm::validate($model)
                );
            }
            $trans = \Yii::$app->db->beginTransaction();
            try {
                $options = Yii::$app->request->post("VoteOption");
                $model->options = $options;
                $model->start_time = strtotime(date("Y-m-d H:i:00", strtotime( $model->start_time)));
                $model->end_time = strtotime(date("Y-m-d H:i:00", strtotime( $model->end_time)));
                $model->updated_at = time();
                $model->read_object = $model->range == 1 ? $model->read_object : [];
                $model->user_department_object = $model->range == 1 && $model->user_department_object ? json_decode($model->user_department_object) : [];
                if($model->status != Vote::STATUS_DRAFT) {
                    if($model->start_time <= time()) {
                        $model->status = Vote::STATUS_GOING;
                    }
                    if($model->end_time <= time()) {
                        $model->status = Vote::STATUS_END;
                    }

                }
                //人员
//                VoteUserObject::deleteAll(['vote_id' => $model->id]);
                WeworkUsers::batchInsertByVote($model, $model->id);
                if($model->range == 0) {
                    //计算总人数
                    $model->total_num = WeworkUsers::find()->count("1");
                }

                if (!$model->save(false)){
                    throw new Exception('model save fail');
                }

                if (!empty($deletedIDs)) {
                    VoteOption::deleteAll(['id' => $deletedIDs]);
                }
                foreach ($modelAttr as $attr) {
                    $attr->vote_id = $model->getPrimaryKey();
                    if($attr->option_image) {
                        $attr->option_image = [$attr->option_image];
                    }
                    if (! $attr->save(false)) {
                        throw new Exception('vote options save fail');
                        break;
                    }
                }
                $trans->commit();
                //发送推送消息
                if($model->status != Vote::STATUS_DRAFT) {
                    try {
                        $newUserArr = [];
                        $time = date("Y-m-d H:i:s", $model->start_time). ' - '. date("Y-m-d H:i:s", $model->end_time);
                        if(!($oldStatus == Vote::STATUS_DRAFT && $model->is_notice == 0)) {
                            if($oldStatus == Vote::STATUS_DRAFT) {
                                if($model->status == Vote::STATUS_GOING) {
                                    $title = "投票已经开始了";
                                } else {
                                    $title = "您收到一条投票邀请";
                                }
                            } else {
                                if($model->status == Vote::STATUS_GOING) {
                                    $title = "投票已经开始了";
                                } else {
                                    $title = "投票变更通知";
                                }
                            }
                            $params = [
                                "title" => "{$title}",
                                "description" => "
    标题：{$model->title}
    投票时间：{$time}
    发布人：{$model->author_name}
    ",
                                "url" => \Yii::$app->params['wework']['frontVoteUrl'].$model->id,
                                "btntxt" => "查看详情",
                            ];
                            if($oldStatus == Vote::STATUS_DRAFT && $model->is_notice == 1) {
                                $usersArr = array_column(VoteUserObject::find()->where(["vote_id" => $model->id])->asArray()->all(), "user_id");
                            } else {
                                $usersArr = array_column(VoteUserObject::find()->where(["is_receive_msg" => 1, "vote_id" => $model->id])->asArray()->all(), "user_id");
                                $newUserArr = array_column(VoteUserObject::find()->where(["is_receive_msg" => 0, "vote_id" => $model->id])->asArray()->all(), "user_id");
                            }
                            if($usersArr) {
                                WeworkSendMsg::send($usersArr, [], [], $params);
                            }

                            if($newUserArr) {
                                $params = [
                                    "title" => "您收到一条投票邀请",
                                    "description" => "
    标题：{$model->title}
    投票时间：{$time}
    发布人：{$model->author_name}
    ",
                                    "url" => \Yii::$app->params['wework']['frontVoteUrl'].$model->id,
                                    "btntxt" => "查看详情",
                                ];

                                WeworkSendMsg::send($newUserArr, [], [], $params);
                            }
                            VoteUserObject::updateAll(["is_receive_msg" => 1], ["vote_id" => $model->id]);
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
                'modelAttr' => (empty($modelAttr)) ? [new VoteOption()] : $modelAttr
            ]);
        }
    }

    /**
     * Deletes an existing Vote model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        VoteOption::deleteAll(['vote_id' => $id]);
        VoteRecord::deleteAll(['vote_id' => $id]);
        VoteUserObject::deleteAll(['vote_id' => $id]);

        return $this->redirect(['index']);
    }

    /**
     * Finds the Vote model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Vote the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Vote::findOne($id)) !== null) {
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
            Vote::deleteAll(['id' => $ids]);
            VoteOption::deleteAll(['vote_id' => $ids]);
            VoteRecord::deleteAll(['vote_id' => $ids]);
            VoteUserObject::deleteAll(['vote_id' => $ids]);
            $trans->commit();
            \Yii::$app->session->setFlash("success", "操作成功");
        } catch (\Exception $e) {
            $trans->rollBack();
            \Yii::$app->session->setFlash("error", "操作失败:" . $e->getMessage());
        }
        return $this->redirect(['index']);
    }

    /**
     * 导出excel
     * @param $ids
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function actionExport($id)
    {
        set_time_limit(0);
        $filename         = "投票选项结果";
        $query            = VoteOption::find()->where(['vote_id' => $id]);
        $response         = \Yii::$app->response;
        $response->format = Response::FORMAT_RAW;
        $response->headers->add('Content-Type', 'application/xlsx');

        $ext   = '.xlsx';
        $title = ["序号", "选项图片", "选项信息", "票数"];

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
                if ($item == "序号") {
                    $value = $key + 1;
                } elseif ($item == "选项图片") {
                    $value = $query_item->option_image ? $query_item->option_image[0] : '';
                } elseif ($item == "选项信息") {
                    $value = $query_item->option_name;
                } else {
                    $value = $query_item->num;
                }

                if($item == "选项图片" && $value) {
                    $i         = $key + 2;
                    $cellIndex = $index[$akey] . $i;

                    $img = file_get_contents($value);
                    $dir = '.'. DIRECTORY_SEPARATOR.'export'.DIRECTORY_SEPARATOR;
                    $file_info = pathinfo($value);
                    $basename = $file_info['basename'];
                    is_dir($dir) OR mkdir($dir, 0777, true); //进行检测文件是否存在
                    file_put_contents($dir . $basename, $img);

                    $image = imagecreatefromstring($img);
                    $width = imagesx($image);
                    $height = imagesy($image);

                    $spreadsheet->getActiveSheet()->getRowDimension($i)->setRowHeight($height/10);
                    $spreadsheet->getActiveSheet()->getColumnDimension($index[$akey])->setWidth(100);

                    $drawing = new Drawing();
                    $drawing->setName($basename);
                    $drawing->setDescription($basename);
                    $drawing->setPath($dir . $basename);
                    $drawing->setWidth($width/10);
                    $drawing->setHeight($height/10);
                    $drawing->setCoordinates($cellIndex);
                    $drawing->setOffsetX(12);
                    $drawing->setOffsetY(12);
                    $drawing->setWorksheet($spreadsheet->getActiveSheet());

                    //设置字体大小
//                    $spreadsheet->getActiveSheet()->getStyle($cellIndex)->getFont()->setName('宋体')->setSize(14);
//                    $spreadsheet->getActiveSheet()->getStyle($cellIndex)->getAlignment()->setWrapText(true);
//                    $spreadsheet->getActiveSheet()->setCellValue($cellIndex, $value);
//                    $spreadsheet->getActiveSheet()->getCell($cellIndex)->getHyperlink()->setUrl($value);
                } else {
                    $i         = $key + 2;
                    $cellIndex = $index[$akey] . $i;
                    //设置字体大小
                    $spreadsheet->getActiveSheet()->getStyle($cellIndex)->getFont()->setName('宋体')->setSize(14);
                    $spreadsheet->getActiveSheet()->getStyle($cellIndex)->getAlignment()->setWrapText(true);
                    $spreadsheet->getActiveSheet()->setCellValue($cellIndex, $value);
                }
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

    public static function curlGet($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 这个是重点 请求https。
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
}

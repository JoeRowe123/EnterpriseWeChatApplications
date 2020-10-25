<?php

namespace backend\controllers;

use backend\models\ArticleCommentSearch;
use backend\models\ArticleLikeSearch;
use backend\models\ArticleReadObjectSearch;
use common\components\wework\WeworkSendMsg;
use common\models\ArticleCategory;
use common\models\ArticleComment;
use common\models\ArticleLike;
use common\models\ArticleReadObject;
use common\models\WeworkUsers;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Yii;
use common\models\Article;
use backend\models\ArticleSearch;
use yii\bootstrap\ActiveForm;
use yii\db\Exception;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * ArticleController implements the CRUD actions for Article model.
 */
class ArticleController extends Controller
{
    public $enableCsrfValidation = false;

    /**
     * Lists all Article models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel       = new ArticleSearch();
        $searchModel->type = Article::TYPE_TYKX;
        $dataProvider      = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'type' => Article::TYPE_TYKX
        ]);
    }

    /**
     * Lists all Article models.
     * @return mixed
     */
    public function actionList()
    {
        $searchModel       = new ArticleSearch();
        $searchModel->type = Article::TYPE_WXT;
        $dataProvider      = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'type' => Article::TYPE_WXT
        ]);
    }

    /**
     * Displays a single Article model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Article model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($type, $id = false)
    {
        $model = new Article();
        $model->loadDefaultValues();
        $model->reading_time = date("Y-m-d H:i:s");
        $model->timing_date = date("Y-m-d H:i:s");
        if($id) {
            $copyModel = $this->findModel($id);
            $model->title = $copyModel->title;
            $model->image = $copyModel->image;
            $model->abstract = $copyModel->abstract;
            $model->is_push_msg = $copyModel->is_push_msg;
            $model->is_important_msg = $copyModel->is_important_msg;
            $model->content = $copyModel->content;
            $model->first_category_id = $copyModel->first_category_id;
            $model->second_category_id = $copyModel->second_category_id;
            $model->third_category_id = $copyModel->third_category_id;
            $model->type = $copyModel->type;
            $model->attachment = $copyModel->attachment;
            $model->is_secrecy = $copyModel->is_secrecy;
            $model->range = $copyModel->range;
            $model->read_object = is_string($copyModel->read_object) ? json_encode(json_decode($copyModel->read_object)) : json_encode($copyModel->read_object);
            $model->user_department_object = json_encode($copyModel->user_department_object);
        }
        if ($model->load(Yii::$app->request->post())) {
            try {
                $trans = Yii::$app->db->beginTransaction();
                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return  ActiveForm::validate($model);
                }

                $model->author_id    = Yii::$app->user->identity->id;
                $model->author_name  = Yii::$app->user->identity->name;
                $model->reading_time = strtotime(date("Y-m-d H:i:00", strtotime($model->reading_time)));
                if($model->timing_date) {
                    $model->timing_date = strtotime(date("Y-m-d H:i:00", strtotime($model->timing_date)));
                }
                $model->read_object = $model->range == 1 ? $model->read_object : [];
                $model->user_department_object = $model->range == 1 && $model->user_department_object ? json_decode($model->user_department_object) : [];
                $model->type         = $type;
                $model->created_at   = time();
                $model->updated_at   = time();
                if (!$model->save()) {
                    throw new \Exception("article save faild:". current($model->getFirstErrors()));
                }

                //人员
                WeworkUsers::batchInsertByArticle($model, $model->primaryKey);
                if($model->range == 0) {
                    //计算总人数
                    $model->total_number = WeworkUsers::find()->count("1");
                    if(!$model->save(false)) {
                        throw new \Exception("编辑人员总人数失败：".current($model->getFirstErrors()));
                    }
                }

                $trans->commit();
                //发送推送消息
                if($model->status == Article::STATUS_ACTIVE && $model->is_push_msg == 1) {
                       try {
                           $info = $model->type == Article::TYPE_TYKX ? "酷讯" : "微学堂";
                           $urlType = $model->type == Article::TYPE_TYKX ? "message" : "school";
                           $params = [
                               "title" => "您收到一个{$info}内容",
                               "description" => "
    标题：{$model->title}
    发布人：{$model->author_name}
    ",
                               "url" => \Yii::$app->params['wework']['frontArticleUrl']."type={$urlType}&id={$model->id}",
                               "btntxt" => "查看详情",
                           ];
                           $usersArr = array_column(ArticleReadObject::find()->where(["article_id" => $model->id])->asArray()->all(), "user_id");
                           if($usersArr) {
                               WeworkSendMsg::send($usersArr, [], [], $params);
                           }
                           ArticleReadObject::updateAll(["is_receive_msg" => 1], ["article_id" => $model->id]);
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
            $index = $model->type == Article::TYPE_TYKX ? 'index' : 'list';
            return $this->redirect([$index]);
        }
        $index = $type == Article::TYPE_TYKX ? 'create' : 'create_wxt';
        return $this->render($index, [
            'model' => $model,
            'type' => $type,
            'id' => $id,
        ]);
    }

    /**
     * Updates an existing Article model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws Exception
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->reading_time = date("Y-m-d H:i:s", $model->reading_time);
        if($model->timing_date) {
            $model->timing_date = date("Y-m-d H:i:s", $model->timing_date);
        }
        $model->read_object = is_string($model->read_object) ? json_encode(json_decode($model->read_object)) : json_encode($model->read_object);
        $model->user_department_object = json_encode($model->user_department_object);
        if ($model->load(Yii::$app->request->post())) {
            $trans = Yii::$app->db->beginTransaction();
            try {
                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return  ActiveForm::validate($model);
                }
                $model->reading_time = strtotime(date("Y-m-d H:i:00", strtotime( $model->reading_time)));
                if($model->timing_date) {
                    $model->timing_date = strtotime(date("Y-m-d H:i:00", strtotime( $model->timing_date)));
                }
                $model->updated_at   = time();
                $model->read_object = $model->range == 1 ? $model->read_object : [];
                $model->user_department_object = $model->range == 1 && $model->user_department_object ? json_decode($model->user_department_object) : [];
                //人员
//                ArticleReadObject::deleteAll(['article_id' => $model->id]);
                WeworkUsers::batchInsertByArticle($model, $model->id);
                if($model->range == 0) {
                    //计算总人数
                    $model->total_number = WeworkUsers::find()->count("1");
                }

                if (!$model->save(false)) {
                   throw new \Exception("article save faild:".current($model->getFirstErrors()));
                }
                $trans->commit();
                //发送推送消息
                if($model->status == Article::STATUS_ACTIVE && $model->is_push_msg == 1) {
                    try {
                        $info = $model->type == Article::TYPE_TYKX ? "酷讯" : "微学堂";
                        $urlType = $model->type == Article::TYPE_TYKX ? "message" : "school";
                        $params = [
                            "title" => "您收到一个{$info}内容",
                            "description" => "
    标题：{$model->title}
    发布人：{$model->author_name}
    ",
                            "url" => \Yii::$app->params['wework']['frontArticleUrl']."type={$urlType}&id={$model->id}",
                            "btntxt" => "查看详情",
                        ];
                        $usersArr = array_column(ArticleReadObject::find()->where(["article_id" => $model->id])->asArray()->all(), "user_id");
                        if($usersArr) {
                            WeworkSendMsg::send($usersArr, [], [], $params);
                        }
                        ArticleReadObject::updateAll(["is_receive_msg" => 1], ["article_id" => $model->id]);
                        \Yii::$app->session->setFlash("success", "操作成功");
                    }catch (\Exception $er) {
                        \Yii::$app->session->setFlash("success", "操作成功,但企业推送消息发送失败:".$er->getMessage());
                    }
                } else {
                    \Yii::$app->session->setFlash("success", "操作成功");
                }
                \Yii::$app->session->setFlash("success", "操作成功");
            } catch (\Exception $e) {
                $trans->rollBack();
                \Yii::$app->session->setFlash("error", "操作失败:" . $e->getMessage());
            }
            $index = $model->type == Article::TYPE_TYKX ? 'index' : 'list';
            return $this->redirect([$index]);
        }
        $index = $model->type == Article::TYPE_TYKX ? 'update' : 'update_wxt';
        return $this->render($index, [
            'model' => $model,
            'type' => $model->type,
        ]);
    }

    /**
     * Deletes an existing Article model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();
        ArticleReadObject::deleteAll(['article_id' => $id]);
        ArticleComment::deleteAll(['article_id' => $id]);
        ArticleLike::deleteAll(['article_id' => $id]);

        $index = $model->type == Article::TYPE_TYKX ? 'index' : 'list';
        return $this->redirect([$index]);
    }

    /**
     * Finds the Article model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Article the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Article::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * 批量删除
     * @param $ids
     * @throws Exception
     */
    public function actionBatchDelete($ids, $type)
    {
        $trans = Yii::$app->db->beginTransaction();
        try {
            $ids = explode(",", $ids);
            Article::deleteAll(['id' => $ids]);
            ArticleReadObject::deleteAll(['article_id' => $ids]);
            ArticleComment::deleteAll(['article_id' => $ids]);
            ArticleLike::deleteAll(['article_id' => $ids]);
            $trans->commit();
            \Yii::$app->session->setFlash("success", "操作成功");
        } catch (\Exception $e) {
            $trans->rollBack();
            \Yii::$app->session->setFlash("error", "操作失败:" . $e->getMessage());
        }
        $index = $type == Article::TYPE_TYKX ? 'index' : 'list';
        return $this->redirect([$index]);
    }

    /**
     * 阅读对象信息
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionReadInfo($id, $status = 1)
    {
        $model                   = $this->findModel($id);
        $searchModel             = new ArticleReadObjectSearch();
        $searchModel->article_id = $id;
        $searchModel->is_read    = $status;
        $dataProvider            = $searchModel->search(Yii::$app->request->queryParams);

        $endCount = ArticleReadObject::find()->where(['article_id' => $id, 'is_read' => 1])->count(1);
        $count = ArticleReadObject::find()->where(['article_id' => $id, 'is_read' => 0])->count(1);
        return $this->render('read', [
            'dataProvider' => $dataProvider,
            'type' => $model->type,
            'endCount' => $endCount,
            'count' => $count,
            'status' => $status,
            'id' => $id,
        ]);
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
        $query            = ArticleReadObject::find()->where(['id' => $ids]);
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
     * 导出全部excel
     * @param $status
     * @param $id
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function actionExportAll($status, $id)
    {
        set_time_limit(0);
        $filename         = "阅读情况";
        $query            = ArticleReadObject::find()->where(['is_read' => $status, 'article_id' => $id]);
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
        $searchModel             = new ArticleCommentSearch();
        $searchModel->article_id = $id;
        $dataProvider            = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('comment', [
            'dataProvider' => $dataProvider,
            'id' => $model->id,
            'type' => $model->type
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
            ArticleComment::updateAll(["del" => 0],['id' => $ids]);
            $trans->commit();
            \Yii::$app->session->setFlash("success", "操作成功");
        } catch (\Exception $e) {
            $trans->rollBack();
            \Yii::$app->session->setFlash("error", "操作失败:" . $e->getMessage());
        }
        return $this->redirect(['comment-info', 'id' => $id]);
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
        $model = ArticleComment::findOne($id);
        if($model) {
            $model->del = 0;
            $model->save(false);
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        return $this->redirect(['comment-info', 'id' => $model->article_id]);
    }

    /**
     * 点赞信息
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionLikeInfo($id)
    {
        $model                   = $this->findModel($id);
        $searchModel             = new ArticleLikeSearch();
        $searchModel->article_id = $id;
        $dataProvider            = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('like', [
            'dataProvider' => $dataProvider,
            'type' => $model->type
        ]);
    }

    /**
     * 分类联动
     */
    public function actionAjaxList()
    {
        if(Yii::$app->request->isAjax){
            $id = Yii::$app->request->get('id');
            if($id) {
                $count = ArticleCategory::find()->where('p_id=:p_id',[':p_id' => $id])->count();
                if($count <= 0) {
                    echo "<option value='0'>请选择内容分类</option>";
                } else {
                    $data = ArticleCategory::find()->where('p_id=:p_id',[':p_id' => $id])->all();
                    echo "<option value='0'>请选择内容分类</option>";
                    foreach ($data as $value) {
                        echo "<option value='$value->id'>$value->name</option>";
                    }
                }

            }else{
                echo "<option value='0'>请选择内容分类</option>";
            }
        }
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
            $model = ArticleReadObject::findOne($id);
            if(!$model) {
               throw new \Exception("数据异常");
            }
            $model->is_receive_msg = 1;
            $model->save(false);
            $info = $model->article->type == Article::TYPE_TYKX ? "酷讯" : "微学堂";
            $urlType = $model->article->type == Article::TYPE_TYKX ? "message" : "school";
            $params = [
                "title" => "您收到一条{$info}内容未阅读，请及时阅读",
                "description" => "
标题：{$model->article->title}
发布人：{$model->article->author_name}
",
                "url" => \Yii::$app->params['wework']['frontArticleUrl']."type={$urlType}&id={$model->article_id}",
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

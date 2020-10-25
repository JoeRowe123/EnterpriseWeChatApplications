<?php

namespace backend\controllers;

use backend\models\OptionForm;
use backend\models\QuestionBankItemSearch;
use common\helpers\FileHelper;
use common\helpers\ModelHelper;
use common\helpers\StringHelper;
use common\models\QuestionBankItem;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Yii;
use common\models\QuestionBank;
use backend\models\QuestionBankSearch;
use yii\base\Model;
use yii\bootstrap\ActiveForm;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * QuestionBankController implements the CRUD actions for QuestionBank model.
 */
class QuestionBankController extends Controller
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
                    'start' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all QuestionBank models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel  = new QuestionBankSearch();
        $searchModel->status = QuestionBank::STATUS_ACTIVE;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single QuestionBank model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $searchModel  = new QuestionBankItemSearch();
        $searchModel->bank_id = $id;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('view', [
            'dataProvider' => $dataProvider,
            'id' => $id,
        ]);
    }

    /**
     * @param $id
     * @return QuestionBankItem|null
     * @throws NotFoundHttpException
     */
    protected function findItemModel($id)
    {
        $model = QuestionBankItem::findOne($id);
        if(!$model) {
            throw new NotFoundHttpException('The requested item does not exist.');
        }
        return $model;
    }
    /**
     * 禁用题目
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionDeleteItem($id)
    {
        $trans = Yii::$app->db->beginTransaction();
        try {
            $model = $this->findItemModel($id);
            $model->status = QuestionBankItem::STATUS_DISABLED;
            $model->save(false);
            if($model->type == QuestionBankItem::TYPE_SINGLE) {
                $model->questionBank->single_num -= 1;
            } else if($model->type == QuestionBankItem::TYPE_MULTIPLE){
                $model->questionBank->multiple_num -= 1;
            } else if($model->type == QuestionBankItem::TYPE_JUDGE) {
                $model->questionBank->judge_num -= 1;
            } else {
                $model->questionBank->gap_filling_num -= 1;
            }
            $model->questionBank->save(false);
            $trans->commit();
            \Yii::$app->session->setFlash("success", "操作成功");
        } catch (\Exception $e) {
            $trans->rollBack();
            \Yii::$app->session->setFlash("error", "操作失败:" . $e->getMessage());
        }
        return $this->redirect(['view', 'id' => $model->bank_id]);
    }

    /**
     * 启用题目
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionStartItem($id)
    {
        $trans = Yii::$app->db->beginTransaction();
        try {
            $model = $this->findItemModel($id);
            $model->status = QuestionBankItem::STATUS_ACTIVE;
            $model->save(false);
            if($model->type == QuestionBankItem::TYPE_SINGLE) {
                $model->questionBank->single_num += 1;
            } else if($model->type == QuestionBankItem::TYPE_MULTIPLE) {
                $model->questionBank->multiple_num += 1;
            } else if($model->type == QuestionBankItem::TYPE_JUDGE) {
                $model->questionBank->judge_num += 1;
            } else {
                $model->questionBank->gap_filling_num += 1;
            }
            $model->questionBank->save(false);
            $trans->commit();
            \Yii::$app->session->setFlash("success", "操作成功");
        } catch (\Exception $e) {
            $trans->rollBack();
            \Yii::$app->session->setFlash("error", "操作失败:" . $e->getMessage());
        }
        return $this->redirect(['view', 'id' => $model->bank_id]);
    }

    /**
     * Creates a new QuestionBank model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new QuestionBank();
        $model->loadDefaultValues();
        if ($model->load(Yii::$app->request->post())) {
            $model->sn          = StringHelper::generateSn('TK');
            $model->author_id   = Yii::$app->user->identity->id;
            $model->author_name = Yii::$app->user->identity->name;
            $model->created_at  = time();
            $model->updated_at  = time();
            $model->save();
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing QuestionBank model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->updated_at = time();
            $model->save(false);
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing QuestionBank model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model         = $this->findModel($id);
        $model->status = QuestionBank::STATUS_DISABLED;
        $model->save(false);

        return $this->redirect(['index']);
    }

    /**
     * 启用
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionStart($id)
    {
        $model         = $this->findModel($id);
        $model->status = QuestionBank::STATUS_ACTIVE;
        $model->save(false);

        return $this->redirect(['index']);
    }

    /**
     * Finds the QuestionBank model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return QuestionBank the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = QuestionBank::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * 添加题目
     * @param $id
     * @return array|string|Response
     * @throws NotFoundHttpException
     */
    public function actionAdd($id)
    {
        $model     = new QuestionBankItem();
        $bankModel = $this->findModel($id);
        $modelAttr = [new OptionForm()];
        if ($model->load(Yii::$app->request->post())) {
            $modelAttr = ModelHelper::createMultiple(OptionForm::class);
            //填充模型
            Model::loadMultiple($modelAttr, Yii::$app->request->post());
            $model->bank_id     = $id;
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ArrayHelper::merge(
                    ActiveForm::validateMultiple($modelAttr),
                    ActiveForm::validate($model)
                );
            }
            $trans = Yii::$app->db->beginTransaction();
            try {
                if ($model->type == QuestionBankItem::TYPE_SINGLE || $model->type == QuestionBankItem::TYPE_MULTIPLE) {
                    $options = Yii::$app->request->post("OptionForm");
                    $resArr  = [];
                    foreach ($options as $k => $option) {
                        $resArr[StringHelper::numToLetter($k)] = $option['name'];
                    }
                    $model->options = $resArr;
                } else {
                    $model->options = "";
                }

                if($model->answer2) {
                    $model->answer = $model->answer2;
                }
                if($model->answer3) {
                    $model->answer = $model->answer3;
                }
                if($model->answer4) {
                    $model->answer = $model->answer4;
                }

                $model->created_at = time();
                $model->updated_at = time();
                if (!$model->save()) {
                    throw new Exception("保存题目失败：" . current($model->getFirstErrors()));
                }

                if ($model->type == QuestionBankItem::TYPE_SINGLE) {
                    $bankModel->single_num += 1;
                } else if ($model->type == QuestionBankItem::TYPE_MULTIPLE) {
                    $bankModel->multiple_num += 1;
                } else if ($model->type == QuestionBankItem::TYPE_JUDGE) {
                    $bankModel->judge_num += 1;
                } else if ($model->type == QuestionBankItem::TYPE_GAP_FILLING) {
                    $bankModel->gap_filling_num += 1;
                }
                $bankModel->total_num  += 1;
                $bankModel->updated_at = time();
                if (!$bankModel->save(false)) {
                    throw new Exception("修改题库数量失败：" . current($model->getFirstErrors()));
                }

                $trans->commit();
                \Yii::$app->session->setFlash("success", "操作成功");
            } catch (\Exception $e) {
                $trans->rollBack();
                \Yii::$app->session->setFlash("error", "操作失败:" . $e->getMessage());
            }
            return $this->redirect(['view', 'id' => $model->bank_id]);
        }

        return $this->render('create-item', [
            'model' => $model,
            'modelAttr' => $modelAttr,
        ]);
    }

    /**
     * 编辑题目
     * @param $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdateItem($id)
    {
        $model = $this->findItemModel($id);
        $bankModel = $this->findModel($model->bank_id);
        $modelAttr = [new OptionForm()];
        if($model->type == QuestionBankItem::TYPE_SINGLE || $model->type == QuestionBankItem::TYPE_MULTIPLE) {
            $resArr  = [];
            foreach ($model->options as $k => $option) {
                $resArr[StringHelper::letterToNum($k)] = $option;
            }
            $modelAttr = [];
            foreach ($resArr as $re) {
                $attr = new OptionForm();
                $attr->name = $re;
                $attr->type = $model->type;
                $modelAttr[] = $attr;
            }
        }

        if($model->type == QuestionBankItem::TYPE_SINGLE) {
            $model->answer4 = $model->answer;
        } else if($model->type == QuestionBankItem::TYPE_MULTIPLE) {
            $model->answer = json_encode($model->answer, JSON_UNESCAPED_UNICODE);
            $model->answer3 = $model->answer;
        } else if($model->type == QuestionBankItem::TYPE_JUDGE) {
            $model->answer2 = $model->answer;
        }

        if ($model->load(Yii::$app->request->post())) {
            $modelAttr = ModelHelper::createMultiple(OptionForm::class);
            Model::loadMultiple($modelAttr, Yii::$app->request->post());
            if ($model->type != QuestionBankItem::TYPE_SINGLE && $model->type != QuestionBankItem::TYPE_MULTIPLE) {
                foreach ($modelAttr as $mAttr) {
                    $mAttr->type = $model->type;
                }
            }
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ArrayHelper::merge(
                    ActiveForm::validateMultiple($modelAttr),
                    ActiveForm::validate($model)
                );
            }
            $trans = Yii::$app->db->beginTransaction();
            try {
                if ($model->type == QuestionBankItem::TYPE_SINGLE || $model->type == QuestionBankItem::TYPE_MULTIPLE) {
                    $options = Yii::$app->request->post("OptionForm");
                    $resArr  = [];
                    foreach ($options as $k => $option) {
                        $resArr[StringHelper::numToLetter($k)] = $option['name'];
                    }
                    $model->options = $resArr;
                } else {
                    $model->options = "";
                }

                if($model->answer2) {
                    $model->answer = $model->answer2;
                }

                if($model->answer3) {
                    $model->answer = $model->answer3;
                }

                if($model->answer4) {
                    $model->answer = $model->answer4;
                }

                $model->updated_at = time();
                if (!$model->save()) {
                    throw new Exception("编辑题目失败：" . current($model->getFirstErrors()));
                }

                $bankModel->updated_at = time();
                if (!$bankModel->save(false)) {
                    throw new Exception("更新题库修改时间失败：" . current($model->getFirstErrors()));
                }

                $trans->commit();
                \Yii::$app->session->setFlash("success", "操作成功");
            } catch (\Exception $e) {
                $trans->rollBack();
                \Yii::$app->session->setFlash("error", "操作失败:" . $e->getMessage());
            }
            return $this->redirect(['view', 'id' => $model->bank_id]);

        } else {
            return $this->render('update-item', [
                'model' => $model,
                'modelAttr' => $modelAttr,
                'id' => $model->bank_id,
            ]);
        }
    }

    /**
     * 下载模板
     * @param $ids
     * @param $bank_id
     * @throws NotFoundHttpException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function actionDownload($ids, $bank_id)
    {
        $ids = explode(",", $ids);
        $bankModel = $this->findModel($bank_id);
        set_time_limit(0);
        $filename         = "试题模板-".$bankModel->name;
        $query            = QuestionBankItem::find()->where(['id' => $ids]);
        $response         = \Yii::$app->response;
        $response->format = Response::FORMAT_RAW;
        $response->headers->add('Content-Type', 'application/xlsx');

        $ext   = '.xlsx';
        $title = ["题目类型", "题目排序", "题目分数", "题目名称", "正确答案"];
        $total_count = 0;
        foreach ($query->each(200) as $key => $query_item) {
            if ($query_item->type == QuestionBankItem::TYPE_SINGLE || $query_item->type == QuestionBankItem::TYPE_MULTIPLE) {
                $count = count($query_item->options);
                if($total_count < $count) {
                    $total_count = $count;
                }
            }
        }
        $arr = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
        $resArr = array_slice($arr, 0, $total_count);

        foreach ($resArr as $res) {
            $title[] = "选项".$res;
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
            $spreadsheet->getActiveSheet()->getStyle($cellIndex)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        }


        foreach ($query->each(200) as $key => $query_item) {
            foreach ($title as $akey => $item) {
                if ($item == "题目类型") {
                    $value = QuestionBankItem::$type[$query_item->type];
                } elseif ($item == "题目排序") {
                    $value = $query_item->sort;
                } elseif ($item == "题目分数") {
                    $value = $query_item->grade;
                } elseif ($item == "题目名称") {
                    $value = $query_item->title;
                } elseif ($item == "正确答案")  {
                    if(in_array($query_item->type, [QuestionBankItem::TYPE_SINGLE, QuestionBankItem::TYPE_JUDGE, QuestionBankItem::TYPE_GAP_FILLING])) {
                        $value = $query_item->answer;
                    } else {
                        $value = implode("",$query_item->answer);
                    }
                } else {
                    if($query_item->type == QuestionBankItem::TYPE_SINGLE || $query_item->type == QuestionBankItem::TYPE_MULTIPLE) {
                        foreach ($query_item->options as $k => $option) {
                            if($item == "选项".$k) {
                                $value = $option;
                                break;
                            } else {
                                $value = "";
                            }
                        }
                    } else {
                        $value = "";
                    }
                }
                $i         = $key + 2;
                $cellIndex = $index[$akey] . $i;
                //设置字体大小
                $spreadsheet->getActiveSheet()->getStyle($cellIndex)->getFont()->setName('宋体')->setSize(14);
                $spreadsheet->getActiveSheet()->getStyle($cellIndex)->getAlignment()->setWrapText(true);
                $spreadsheet->getActiveSheet()->setCellValue($cellIndex, $value);
                $spreadsheet->getActiveSheet()->getStyle($cellIndex)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
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
     * 批量导入
     * @param $id
     * @return string|Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     */
    public function actionBatchImport($id)
    {
        ini_set('memory_limit','3072M');
        set_time_limit(0);
        $model = $this->findModel($id);
        if (\Yii::$app->request->isPost) {
            $trans = Yii::$app->db->beginTransaction();
            try {
                $file = UploadedFile::getInstance($model, 'batch_import');
                if(!$file) {
                    throw new \Exception("请选择一个文件");
                }
                FileHelper::batchImportPrice($id, $file->tempName);
                $trans->commit();
                Yii::$app->session->setFlash("success", "导入成功");
            }catch (\Exception $e) {
                $trans->rollBack();
                Yii::$app->session->setFlash("error", "导入失败：".$e->getMessage());
            }
            return $this->redirect(['view','id' => $id]);
        } else {
            return $this->renderAjax("import", [
                "model" => $model
            ]);
        }
    }
}

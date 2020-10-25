<?php

namespace backend\controllers;

use backend\models\SignupForm;
use common\helpers\StringHelper;
use common\models\Task;
use common\models\WeworkUsers;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Yii;
use common\models\User;
use backend\models\UserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
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
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionExport()
    {
        set_time_limit(0);
        $filename         = "所有人员";
        $query            = WeworkUsers::find();
        $response         = \Yii::$app->response;
        $response->format = Response::FORMAT_RAW;
        $response->headers->add('Content-Type', 'application/xlsx');

        $ext   = '.xlsx';
        $title = ["姓名", "员工号", "职位", "手机", "部门"];

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
                    $value = $query_item->user_id;
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
}

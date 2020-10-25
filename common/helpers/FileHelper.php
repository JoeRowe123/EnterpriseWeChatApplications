<?php
/**
 * Created by PhpStorm.
 * User: MrDong
 * Date: 2019/8/9
 * Time: 15:51
 */

namespace common\helpers;


use common\models\Goods;
use common\models\GoodsCompose;
use common\models\GoodsPrice;
use common\models\QuestionBank;
use common\models\QuestionBankItem;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use yii\db\Exception;

class FileHelper
{
    /**
     * @param int $id
     * @param string $file
     * @param int $columnCnt
     * @param array $options
     * @throws Exception
     * @throws \Throwable
     */
    public static function batchImportPrice($id, string $file = '', int $columnCnt = 0, &$options = [])
    {
        try {
            $goods = QuestionBank::findOne($id);
            if(!$goods) {
                throw new \Exception(sprintf("对应的题库不存在"));
            }
            /* 转码 */
            $file = iconv("utf-8", "gb2312", $file);

            if (empty($file) OR !file_exists($file)) {
                throw new \Exception('文件不存在!');
            }

            /** @var Xlsx $objRead */
            $objRead = IOFactory::createReader('Xlsx');

            if (!$objRead->canRead($file)) {
                /** @var Xls $objRead */
                $objRead = IOFactory::createReader('Xls');

                if (!$objRead->canRead($file)) {
                    throw new \Exception('只支持导入Excel文件！');
                }
            }

            /* 如果不需要获取特殊操作，则只读内容，可以大幅度提升读取Excel效率 */
            empty($options) && $objRead->setReadDataOnly(true);
            /* 建立excel对象 */
            $obj = $objRead->load($file);
            $sheetCount = $obj->getSheetCount();

            $resArr = [];
            for ($s_count = 0; $s_count < $sheetCount; $s_count++) {
                /* 获取指定的sheet表 */
                $currSheet = $obj->getSheet($s_count);

                if (isset($options['mergeCells'])) {
                    /* 读取合并行列 */
                    $options['mergeCells'] = $currSheet->getMergeCells();
                }

                if (0 == $columnCnt) {
                    /* 取得最大的列号 */
                    $columnH = $currSheet->getHighestColumn();
                    /* 兼容原逻辑，循环时使用的是小于等于 */
                    $columnCnt = Coordinate::columnIndexFromString($columnH);
                }

                /* 获取总行数 */
                $rowCnt = $currSheet->getHighestRow();
                $data   = [];

                /* 读取内容 */
                for ($_row = 1; $_row <= $rowCnt; $_row++) {
                    $isNull = true;

                    for ($_column = 1; $_column <= $columnCnt; $_column++) {
                        $cellName = Coordinate::stringFromColumnIndex($_column);
                        $cellId   = $cellName . $_row;
                        $data[$_row][$cellName] = trim($currSheet->getCell($cellId)->getFormattedValue());

                        if (!empty($data[$_row][$cellName])) {
                            $isNull = false;
                        }
                    }

                    /* 判断是否整行数据为空，是的话删除该行数据 */
                    if ($isNull) {
                        unset($data[$_row]);
                    }
                }

                $resArr[] = $data;
            }

            $result = [];
            $single_num = 0;
            $multiple_num = 0;
            $judge_num = 0;
            $gap_filling_num = 0;
            $totalNum = 0;

            foreach ($resArr as $res) {
               unset($res[1]);
               foreach ($res as $item) {
                   if(!trim($item["A"]) || !trim($item["B"]) || !trim($item["C"]) || !trim($item["D"]) || !trim($item["E"])) {
                       throw new \Exception("导入数据格式不正确，请确认以后再重新上传");
                   }
                    $type = QuestionBankItem::$typeEnum[trim($item["A"])];
                    if($type == 1) {
                        $single_num += 1;
                    } elseif($type == 2) {
                        $multiple_num += 1;
                    } elseif($type == 3) {
                        $judge_num += 1;
                    } elseif($type == 4) {
                        $gap_filling_num += 1;
                    }
                   $totalNum += 1;
                    $answer = trim($item["E"]);
                    if($type == QuestionBankItem::TYPE_SINGLE || $type == QuestionBankItem::TYPE_MULTIPLE ) {
                        if(!preg_match("/^[a-zA-Z\s]+$/",$answer)){
                            throw new \Exception("单选题或多选题的正确答案中存在其它字符（只允许为字母），请确认以后再重新上传");
                        }
                        $answer = strtoupper($answer);
                    }
                    if($type == QuestionBankItem::TYPE_MULTIPLE) {
                        $answer = str_split($answer);
                    }
                    $options = "";
                    if($type == QuestionBankItem::TYPE_MULTIPLE || $type == QuestionBankItem::TYPE_SINGLE) {
                        $itemCount = count($item);
                        $optionArr = [];
                        $arr = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
                        $j = 0;
                        for ($i = 5; $i < $itemCount; $i++) {
                            if($item[$arr[$i]]) {
                                $optionArr[$arr[$j]] = $item[$arr[$i]];
                            }
                            $j++;
                        }
                        $options = $optionArr;
                    }

                   $result[] = [
                        $id,
                        $type,
                        trim($item["B"]) ?? 0,
                        trim($item["C"]),
                        trim($item["D"]),
                        $options,
                        $answer,
                        time(),
                        time()
                   ];
               }
            }

            \Yii::$app->db->createCommand()->batchInsert('question_bank_item', ['bank_id', 'type', 'sort', 'grade', 'title', 'options', 'answer', 'created_at', 'updated_at'], $result)->execute();

            $goods->single_num += $single_num;
            $goods->multiple_num+= $multiple_num;
            $goods->judge_num += $judge_num;
            $goods->gap_filling_num += $gap_filling_num;
            $goods->total_num += $totalNum;
            $goods->save(false);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
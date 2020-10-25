<?php
/**
 * Created by PhpStorm.
 * User: MrDong
 * Date: 2019/11/6
 * Time: 22:12
 */

namespace backend\controllers;


use backend\models\QuestionBankItemSearch;
use common\components\wework\InitCorp;
use common\models\ExaminationPaper;
use common\models\QuestionBank;
use common\models\QuestionBankItem;
use common\models\User;
use yii\data\ArrayDataProvider;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ApiController extends Controller
{
    public function beforeAction($action)
    {
        $response = \Yii::$app->response;
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $response->getHeaders()->set('Access-Control-Allow-Origin', '*');
        $response->getHeaders()->set('Access-Control-Allow-Headers', 'accept, cache-control, token, content-type');
        $response->getHeaders()->set('Access-Control-Allow-Methods', 'GET, POST, PUT,DELETE, OPTIONS');
        return parent::beforeAction($action); // TODO: Change the autogenerated stub
    }

    /**
     * 所有题库
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionAllQuestionBank()
    {
        return QuestionBank::find()->with(["items" => function($q) {
            return $q->where(['status' => QuestionBankItem::STATUS_ACTIVE]);
        }])->where(['status' => QuestionBank::STATUS_ACTIVE])->orderBy("id desc")->asArray()->all();
    }

    /**
     * 自主选题
     * @param int $pageSize
     * @return array
     */
    public function actionAllTopic($pageSize = 15)
    {
        $body = \Yii::$app->request->post();
        $searchModel = new QuestionBankItemSearch();
        $searchModel->bank_id = $body['ids'];
        $searchModel->status = QuestionBankItem::STATUS_ACTIVE;
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams, $pageSize);

        return [
            'items'    => $dataProvider->getModels(),
            'total'    => $dataProvider->totalCount,
            'pageSize' => $pageSize
        ];
    }

    /**
     * 自主选题
     * @param int $pageSize
     * @return array
     */
    public function actionTopics()
    {
        $body = json_decode(\Yii::$app->request->rawBody, true);
        return QuestionBankItem::find()->with("questionBank")->where(['bank_id' => $body['ids'], 'status' => QuestionBankItem::STATUS_ACTIVE])->asArray()->all();
    }

    /**
     * 固定规则随机生成题目
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function actionRandomTopic()
    {
        $body = json_decode(\Yii::$app->request->rawBody, true);
        $ids = $body["ids"];
        $topicArr = $body["topicArr"];

        $singleAllIds = array_column(QuestionBankItem::find()->where(['bank_id' => $ids, 'type' => QuestionBankItem::TYPE_SINGLE, 'status' => QuestionBankItem::STATUS_ACTIVE])->all(), 'id');
        $multipleAllIds = array_column(QuestionBankItem::find()->where(['bank_id' => $ids, 'type' => QuestionBankItem::TYPE_MULTIPLE])->all(), 'id');
        $judgeAllIds = array_column(QuestionBankItem::find()->where(['bank_id' => $ids, 'type' => QuestionBankItem::TYPE_JUDGE])->all(), 'id');
        $gapFillingAllIds = array_column(QuestionBankItem::find()->where(['bank_id' => $ids, 'type' => QuestionBankItem::TYPE_GAP_FILLING])->all(), 'id');

        $singleRes = [];
        if($body['searchRes']) {
            foreach ($body['searchRes'] as $searchItem) {
                $results = QuestionBankItem::find()->with("questionBank")->where(['id' => $searchItem['resIds']])->asArray()->all();
                foreach ($results as $item) {
                    $res['item'] = $item;
                    $res['grade'] = $topicArr[$item['type']]['grade'];
                    $singleRes[] = $res;
                }
            }
            $searchRes = $body['searchRes'];
        } else {
            $searchRes = [];
            foreach ($topicArr as $k => $arr) {
                if($k == QuestionBankItem::TYPE_SINGLE) {
                    $resArr = $singleAllIds;
                } else if($k == QuestionBankItem::TYPE_MULTIPLE) {
                    $resArr = $multipleAllIds;
                } else if($k == QuestionBankItem::TYPE_JUDGE) {
                    $resArr = $judgeAllIds;
                } else {
                    $resArr = $gapFillingAllIds;
                }

                $num = $arr['num'] ?? 0;
                if($num > count($resArr)) {
                    $num = count($resArr);
                }
                if($num == 0) {
                    $idsArr = [];
                } else {
                    $idsArr = $resArr ? array_rand($resArr, $num) : [];
                }
                $resIds = [];
                if($idsArr || $idsArr === 0) {
                    if(is_array($idsArr)) {
                        foreach ($idsArr as $index) {
                            $searchRes[] = [
                                "resIds" => $resArr[$index],
                                "grade" => $arr['grade']
                            ];
                            $resIds[] = $resArr[$index];
                        }
                    } else {
                        $searchRes[] = [
                            "resIds" => $resArr[$idsArr],
                            "grade" => $arr['grade']
                        ];
                        $resIds = $resArr[$idsArr];
                    }
                }

                $results = QuestionBankItem::find()->with("questionBank")->where(['id' => $resIds])->asArray()->all();
                foreach ($results as $item) {
                    $res['item'] = $item;
                    $res['grade'] = $arr['grade'];
                    $singleRes[] = $res;
                }

            }
        }

        $pageSize = \Yii::$app->request->queryParams && isset(\Yii::$app->request->queryParams['pageSize']) ? \Yii::$app->request->queryParams['pageSize'] : 5;
        $provider = new ArrayDataProvider([
            'allModels' => $singleRes,
            'pagination' => [
                'pageSize' => $pageSize,
                'page'     => \Yii::$app->request->queryParams && isset(\Yii::$app->request->queryParams['page']) ? \Yii::$app->request->queryParams['page'] - 1 : 0
            ],
            'sort' => [
                'attributes' => ['id', 'name'],
            ],
        ]);
        $datas = [];
        foreach ($provider->getModels() as $val) {
            $datas[] = $val;
        }
        return [
            'searchRes' => $searchRes,
            'items'    => $datas,
            'total'    => $provider->totalCount,
            'pageSize' => $pageSize,
            'totalItems' => $singleRes
        ];
    }

    /**
     * 随机生成题目
     * @return array
     */
    public function actionRandom()
    {
        $body = json_decode(\Yii::$app->request->rawBody, true);
        $ids = $body["ids"];
        $topicArr = $body["topicArr"];

        $singleAllIds = array_column(QuestionBankItem::find()->where(['bank_id' => $ids, 'type' => QuestionBankItem::TYPE_SINGLE, 'status' => QuestionBankItem::STATUS_ACTIVE])->all(), 'id');
        $multipleAllIds = array_column(QuestionBankItem::find()->where(['bank_id' => $ids, 'type' => QuestionBankItem::TYPE_MULTIPLE])->all(), 'id');
        $judgeAllIds = array_column(QuestionBankItem::find()->where(['bank_id' => $ids, 'type' => QuestionBankItem::TYPE_JUDGE])->all(), 'id');
        $gapFillingAllIds = array_column(QuestionBankItem::find()->where(['bank_id' => $ids, 'type' => QuestionBankItem::TYPE_GAP_FILLING])->all(), 'id');

        $singleRes = [];
        foreach ($topicArr as $k => $arr) {
            if($k == QuestionBankItem::TYPE_SINGLE) {
                $resArr = $singleAllIds;
            } else if($k == QuestionBankItem::TYPE_MULTIPLE) {
                $resArr = $multipleAllIds;
            } else if($k == QuestionBankItem::TYPE_JUDGE) {
                $resArr = $judgeAllIds;
            } else {
                $resArr = $gapFillingAllIds;
            }
            $num = $arr['num'] ?? 0;
            if($num > count($resArr)) {
                $num = count($resArr);
            }
            if($num == 0) {
                $idsArr = [];
            } else {
                $idsArr = $resArr ? array_rand($resArr, $num) : [];
            }

            $resIds = [];
            if($idsArr || $idsArr === 0) {
                if(is_array($idsArr)) {
                    foreach ($idsArr as $index) {
                        $resIds[] = $resArr[$index];
                    }
                } else {
                    $resIds = $resArr[$idsArr];
                }
            }
            $singleRes[$k]['items'] = QuestionBankItem::find()->with("questionBank")->where(['id' => $resIds])->asArray()->all();
            $singleRes[$k]['grade'] = $arr['grade'];
        }

        return $singleRes;
    }

    /**
     * 获取所有部门及成员
     * @return array|mixed
     * @throws \Exception
     */
    public function actionGetDepartmentUserInfo()
    {
        set_time_limit(0);
        if(\Yii::$app->cache->exists("departmentUserInfo")) {
            return json_decode(\Yii::$app->cache->get("departmentUserInfo"), true);
        } else {
            $wework = InitCorp::init();
            $departmentList = User::getWeWorkUsers($wework);
            \Yii::$app->cache->set("departmentUserInfo", json_encode($departmentList));

            return $departmentList;
        }
    }


    /**
     * 获取企业微信部门列表
     * @return array
     */
    public static function actionGetAllDepartment()
    {
        $wework = new \CorpAPI(\Yii::$app->params['wework']['corpId'], \Yii::$app->params['wework']['secret']);
        $departmentList = $wework->DepartmentList();
        return $departmentList;

    }

    /**
     * 获取企业微信部门下的成员
     * @return array
     */
    public static function actionGetAllUserByDepartment($id)
    {
        $wework = new \CorpAPI(\Yii::$app->params['wework']['corpId'], \Yii::$app->params['wework']['secret']);
        return $wework->UserSimpleList((int)$id,0);
    }

    /**
     * 试卷详情
     * @param $id
     * @return array|\yii\db\ActiveRecord[]
     * @throws NotFoundHttpException
     */
    public static function actionPaperDetail($id)
    {
        $model = ExaminationPaper::find()
            ->with(['users', 'items'])
            ->where(['id' => $id])
            ->asArray()
            ->one();
        if(!$model) {
            throw new NotFoundHttpException("数据错误");
        }
        $model['start_time'] = date("Y-m-d H:i:s", $model['start_time']);
        $model['end_time'] = date("Y-m-d H:i:s", $model['end_time']);
        foreach($model['items'] as &$item) {
            $item['item_option'] = json_decode($item['item_option'], true);
            $item['item_answer'] = json_decode($item['item_answer'], true);
        }
        return $model;
    }


}
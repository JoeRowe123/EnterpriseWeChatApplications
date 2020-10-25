<?php

namespace backend\controllers;

use common\helpers\StringHelper;
use Yii;
use common\models\Task;
use backend\models\TaskSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TaskController implements the CRUD actions for Task model.
 */
class TaskController extends Controller
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
     * Lists all Task models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TaskSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Task model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Task();
        $model->sn = StringHelper::generateSn("Task");
        $model->name = "手动刷新企业微信成员信息";
        $model->created_at = time();
        $model->updated_at = time();
        if($model->save()) {
            \Yii::$app->session->setFlash("success", "操作成功，任务执行可能需要一段时间，请待任务执行成功以后再进行相关操作，谢谢。");
        } else {
            \Yii::$app->session->setFlash("error", "操作失败:" . current($model->getFirstErrors()));
        }
        return $this->redirect(['index']);
    }
}

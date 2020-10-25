<?php
namespace console\controllers;

use common\models\Task;
use common\models\User;
use common\models\WeworkUsers;
use yii\base\Exception;
use yii\console\Controller;

class UserController extends Controller
{
    /**
     * 这里创建的管理员为超级管理员
     * @param $username
     * @param $password
     * @param $email
     *
     * @return bool|int
     * @throws \yii\db\Exception
     */
    public function actionCreate($username, $password, $email)
    {
        $trans = \Yii::$app->getDb()->beginTransaction();
        try {
            User::createUser($username, $password, $email);
        } catch (Exception $e) {
            $trans->rollBack();

            return $this->stderr($e->getMessage());
        }
        $trans->commit();

        return $this->stdout('OK');
    }

    /**
     * 刷新企业微信成员缓存（自动刷新）
     * @throws \Exception
     */
    public function actionRefreshWeworkUser()
    {
        WeWorkUsers::refreshData();
    }

    /**
     * 执行手动生成的刷新任务
     */
    public function actionTask()
    {
        $models = Task::find()->where(['status' => 'wait'])->all();
        foreach ($models as $model) {
            /* @var $model Task */
            $model->status = 'process';
            $model->save(false);
        }

        $trans = \Yii::$app->db->beginTransaction();
        try {
            foreach ($models as $model) {
                try {
                    WeworkUsers::refreshData();
                    $model->status = 'success';
                    $model->msg = "执行成功";
                } catch (\Exception $ev) {
                    $model->status = 'error';
                    $model->msg = $ev->getMessage();
                }
                $model->save(false);
            }
            $trans->commit();
            echo "执行成功".PHP_EOL;
        } catch (\Exception $e) {
            $trans->rollBack();
            \Yii::error("执行任务失败：".$e->getMessage().$e->getTraceAsString());
            echo "执行任务失败".$e->getMessage().PHP_EOL;
        }

    }
}
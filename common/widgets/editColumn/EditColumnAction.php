<?php

namespace common\widgets\editColumn;

use common\helpers\ActiveFormHelper;
use yii\base\Action;
use yii\base\Exception;

class EditColumnAction extends Action
{

    public $modelClass;

    public function run()
    {
        \Yii::$app->getResponse()->format = 'json';
        try {
            $obj   = \Yii::createObject($this->modelClass);
            $model = $obj::findOne(\Yii::$app->request->post("key"));
        } catch (\Exception $e) {
            return [
                'msg'  => '设置失败,' . $e,
                "code" => -1
            ];
        }
        $model->load(\Yii::$app->request->post("value"), '');
        if ($str = ActiveFormHelper::parseValidateResult($model)) {
            return [
                'msg'  => '设置失败,' . $str,
                "code" => -1
            ];
        }
        try {
            $model->save(false);
            return [
                'msg'  => '设置成功',
                "code" => 0
            ];
        } catch (Exception $e) {
            return [
                'msg'  => $e->getMessage(),
                "code" => 0
            ];
        }
    }
}
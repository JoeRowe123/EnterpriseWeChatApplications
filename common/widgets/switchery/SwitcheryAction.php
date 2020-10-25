<?php

namespace common\widgets\switchery;

use common\helpers\ActiveFormHelper;
use yii\base\Action;
use yii\base\Exception;

/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2018/12/1
 * Time: 6:15 PM
 */
class SwitcheryAction extends Action
{

    public $modelClass;

    public $attr = 'status';

    public function run()
    {
        \Yii::$app->getResponse()->format = 'json';
        try {
            $obj   = \Yii::createObject($this->modelClass);
            $model = $obj::findOne(\Yii::$app->request->get("key"));
        } catch (\Exception $e) {
            return [
                'msg'  => '设置失败,' . $e,
                "code" => -1
            ];
        }
        $model->{$this->attr} = \Yii::$app->request->get("status");
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
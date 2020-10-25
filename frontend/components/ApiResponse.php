<?php

namespace frontend\components;

use yii\web\Response;

class ApiResponse extends Response
{
    public $exceptActions = [];
    public $format = Response::FORMAT_JSON;

    public function send()
    {
        if (!in_array(\Yii::$app->requestedRoute, $this->exceptActions)) {
            if ($this->statusCode !== 200) {
                $this->data = [
                    'code' => $this->statusCode ?? 500,
                    'message' => ($this->data['message'] ?? '') == '' ? '未定义的错误' : $this->data['message'],
                    'extra' => YII_DEBUG ? $this->data : null,
                    'data' => null
                ];
            } else {
                $this->data = [
                    'code' => 0,
                    'message' => 'ok',
                    'extra' => null,
                    'data' => $this->data
                ];
            }
            $this->statusCode = 200;
        }
        parent::send();
    }

}
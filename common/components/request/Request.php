<?php
namespace common\components\request;

use yii\web\BadRequestHttpException;
use yii\web\Request as BaseRequest;

/**
 * Created by PhpStorm.
 * User: hu yang
 * Date: 2018/1/26
 * Time: 下午5:09
 */
class Request extends BaseRequest
{


    protected $currentRawBody;

    /**
     * @throws \yii\base\ExitException
     */
    public function init()
    {
        if ($this->getMethod() === 'OPTIONS') {
            \Yii::$app->end();
        }
    }


    /**
     * @param $key
     *
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function body($key = null)
    {
        if ($this->currentRawBody === null) {
            $this->currentRawBody = json_decode($this->getRawBody(), true);
        }
        if ($key === null) {
            return $this->currentRawBody;
        }
        $argc = explode('.', $key);
        $temp = $this->currentRawBody;
        foreach ($argc as $item) {
            if (!isset($temp[$item])) {
                throw  new BadRequestHttpException("缺少参数$item");
            }
            $temp = $temp[$item];
        }

        return $temp;
    }


}
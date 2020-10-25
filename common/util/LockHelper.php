<?php

namespace common\util;

use yii\base\Exception;

class LockHelper
{
    const LOCK_TIME = 60;

    /**
     * 多线程同步方法锁
     * @param $key
     * @param $fun
     * @return mixed
     * @throws Exception
     */
    public static function sync($key, $fun)
    {
        $lock = RedisHelper::inc($key, self::LOCK_TIME);
        try {
            if ($lock > 1) {
                $backtrace = debug_backtrace();
                array_shift($backtrace);
                throw new Exception('系统繁忙或重复支付,请稍后再试' . $backtrace[0]['function']);
            }
            return call_user_func($fun);
        } finally {
            RedisHelper::dec($key);
        }
    }

}
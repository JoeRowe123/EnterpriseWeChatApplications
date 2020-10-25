<?php

namespace common\util;

use Yii;
use yii\base\ErrorException;

/**
 * Created by PhpStorm.
 * User: hu yang
 * Date: 2018/2/8
 * Time: 上午10:29
 */
final class RedisHelper
{

    final public static function getKey($key)
    {
        return md5($key);
    }

    /**
     * redis 计数器 +1
     *
     * @param     $key
     * @param int $expire
     *
     * @return array|bool|null|string
     */
    final public static function inc($key, $expire)
    {
        $key = self::getKey($key);

        $count = Yii::$app->redis->executeCommand('INCR', [$key]);
        if ($count == 1) {
            Yii::$app->redis->executeCommand('EXPIRE', [$key, $expire]);
        }

        return $count;
    }

    /**
     * redis计数器-减少
     * @param $key
     *
     * @return array|bool|null|string
     */
    public static function dec($key)
    {
        $key = self::getKey($key);

        return Yii::$app->redis->executeCommand('DECR', [$key]);
    }

    /**
     * 排他锁
     *
     * @param           $key
     * @param float|int $expire
     *
     * @return bool
     * @throws \yii\base\ErrorException
     */
    public static function exclusionLock($key, $expire = 60 * 10)
    {
        $count = Yii::$app->redis->executeCommand('INCR', [$key]);
        if ($count > 1) {
            throw new ErrorException('Lock Failed , 正在被处理,请稍后重试');
        }

        Yii::$app->redis->executeCommand('EXPIRE', [$key, $expire]);

        return $count;
    }

    /**
     * 移除锁
     *
     * @param $key
     *
     * @return mixed
     */
    public static function exclusionUnlock($key)
    {
        return Yii::$app->redis->executeCommand('DEL', [$key]);
    }
}
<?php
namespace common\helpers;

use Yii;

/**
 * Created by PhpStorm.
 * User: hu yang
 * Date: 2018/2/8
 * Time: 上午10:29
 */
final class RedisHelper
{

    final static function getKey($key)
    {
        return md5($key.__CLASS__);
    }

    /**
     * redis 计数器 +1
     *
     * @param     $key
     * @param int $expire
     *
     * @return array|bool|null|string
     */
    final static function inc($key, $expire)
    {
        $key   = self::getKey($key);

        $count = Yii::$app->redis->executeCommand('INCR', [$key]);
        if ($count == 1) {
            Yii::$app->redis->executeCommand('EXPIRE', [$key, $expire]);
        }

        return $count;
    }

    /**
     * redis计数器-减少
     * @param $key
     * @return array|bool|null|string
     */
    public static function dec($key)
    {
        $key = self::getKey($key);
        return Yii::$app->redis->executeCommand('DECR', [$key]);
    }

}
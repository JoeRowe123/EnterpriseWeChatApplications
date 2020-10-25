<?php
namespace common\helpers;
/**
 * Created by PhpStorm.
 * User: hu yang
 * Date: 2018/2/7
 * Time: 下午3:39
 */
final class TimeHelper
{
    /**
     * @param int $time
     *
     * @return string
     */
    public static function timeFormat(int $time):string
    {
        $now = time();
        $today = strtotime(date('y-m-d',$now));
        $diff = $now - $time;
        $str = '';
        switch($time){
            case $diff < 60 :
                $str = $diff . '秒前';
                break;
            case $diff < 3600 :
                $str = floor($diff / 60) . '分钟前';
                break;
            case $diff < (3600 * 8) :
                $str = floor($diff / 3600) . '小时前';
                break;
            case $time > $today :
                $str = '今天' . date('H:i', $time);
                break;
            default :
                $str = date('Y-m-d' , $time);
        }
        return $str;
    }
}
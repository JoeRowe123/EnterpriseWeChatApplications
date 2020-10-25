<?php

namespace common\helpers;

/**
 * Created by PhpStorm.
 * User: hu yang
 * Date: 2018/2/7
 * Time: 下午5:41
 */
final class DateHelper
{

    /**
     * 获取给定时间 Y-m-d H:i:s
     *
     * @param int null $time
     *
     * @return string
     */
    final static function getDateTime(int $time = null): string
    {
        return date('Y-m-d H:i:s', $time ?? time());
    }

    /**
     * 获取当前时间 Y-m-d H:i:s
     *
     * @return string
     */
    final static function getCurrentDateTime(): string
    {
        return self::getDateTime();
    }

    /**
     * 比较两个时间大小
     *
     * @param $date1
     * @param $date2
     * @param $type 'hour','day'
     *
     * @return int
     */
    final public static function compareDate($date1, $date2, $type = 'day')
    {
        $time1 = strtotime($date1);
        $time2 = strtotime($date2);
        if ($type == 'day') {
            return round(($time1 - $time2) / 60 / 60 / 24);
        }
        if ($type == 'hour') {
            return round((($time1 - $time2) / 60 / 60));
        }
        if ($type == 'second') {
            return $time1 - $time2;
        }

        return false;
    }

    /**
     * @param $unix_timestamp
     *
     * @return false|int|string
     */
    public static function getTodayStart($unix_timestamp = true)
    {
        $time = strtotime('today');

        return $unix_timestamp ? $time : date('Y-m-d H:i:s', $time);
    }

    /**
     * 计算两组时间是否有交集
     * @param string $beginTime1
     * @param string $endTime1
     * @param string $beginTime2
     * @param string $endTime2
     * @return bool
     */
    public static function isTimeCross($beginTime1 = '', $endTime1 = '', $beginTime2 = '', $endTime2 = '')
    {
        $status = $beginTime2 - $beginTime1;
        if ($status > 0) {
            $status2 = $beginTime2 - $endTime1;
            if ($status2 >= 0) {
                return false;
            } else {
                return true;
            }
        } else {
            $status2 = $endTime2 - $beginTime1;
            if ($status2 > 0) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * 获取时间区间内所有时间
     * @param $start_time
     * @param $end_time
     * @return mixed
     * @throws \Exception
     */
    public static function periodDate($start_time,$end_time){
        $start_time = strtotime($start_time);
        $end_time = strtotime($end_time);
        if($start_time > $end_time) {
            throw new \Exception("开始时间大于结束时间");
        }
        $i=0;
        while ($start_time<=$end_time){
            $arr[$i]=date('Y-m-d',$start_time);
            $start_time = strtotime('+1 day',$start_time);
            $i++;
        }
        return $arr;
    }

    /**
     * 获取时间区间内的周几日期
     * @param $start_time
     * @param $end_time
     * @param $weekDay
     * @return array
     */
    public static function getDateByWeek($start_time,$end_time, $weekDay)
    {
        $start_date = strtotime($start_time);
        $end_date = strtotime($end_time);
        $days = ($end_date - $start_date) / 86400;
        $data = [];

        for ($i=0; $i < $days; $i++) {
            $num_week = date('w',$start_date+($i*86400));
            if($num_week == $weekDay) {
                $data[] = date("Y-m-d", $start_date+($i*86400));
            }
        }

        return $data;
    }
}
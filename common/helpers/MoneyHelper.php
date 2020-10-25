<?php
namespace common\helpers;
/**
 * Created by PhpStorm.
 * User: hu yang
 * Date: 2018/1/30
 * Time: 上午11:02
 */
final class MoneyHelper
{
    /**
     * @param int    $value
     *
     * @param string $scale
     *
     * @return float
     */
    public final static function f2y(int $value,$scale = '2'):float
    {
        return bcdiv($value ,100,$scale);
    }

    /**
     * @param float  $value
     *
     * @param string $scale
     *
     * @return int
     */
    public final static function y2f(float $value,$scale = '2'):int
    {
        return bcmul($value,100,$scale);
    }


}
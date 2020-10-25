<?php

namespace common\helpers;
/**
 * Created by PhpStorm.
 * User: hu yang
 * Date: 2018/3/21
 * Time: 下午5:55
 */
class StringHelper
{
    /**
     * @param $prefix
     *
     * @return string
     */
    public static function generateSn(string $prefix): string
    {
        $str = substr(YII_BEGIN_TIME, 11, 3) . strtoupper(substr(uniqid(), 10, 3));

        return $prefix . date('ymdHis') . $str;
    }

    /**
     * @param $str
     * @return mixed
     */
    public static function formatPhoneNumber($str)
    {
        if (preg_match("/^1[123456789]{1}\d{9}$/", $str)) {
            return substr_replace($str, '****', 3, 4);
        } else {
            return '';
        }

    }

    /**
     * 生成随机字符串
     * @param $length
     * @return string
     */
    public static function randomkeys($length)
    {
        $pattern = '1234567890abcdefghijklmnopqrstuvwxyz   
               ABCDEFGHIJKLOMNOPQRSTUVWXYZ';
        $key     = '';
        for ($i = 0; $i < $length; $i++) {
            $key .= $pattern{mt_rand(0, 35)};    //生成php随机数
        }
        return $key;
    }

    /**
     * 汉字第一个字母
     * @param $str
     * @return string
     */
    public static function getFirstCharter($str)
    {
        if (empty($str)) {
            return '#';
        }

        $fchar = ord($str{0});

        if ($fchar >= ord('A') && $fchar <= ord('z'))
            return strtoupper($str{0});

        $s1 = iconv('UTF-8', 'gb2312//TRANSLIT//IGNORE', $str);

        $s2 = iconv('gb2312', 'UTF-8//TRANSLIT//IGNORE', $s1);

        $s = $s2 == $str ? $s1 : $str;
        if (isset($s{0}) && isset($s{1})) {
            $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
        } else {
            $asc = 0;
        }


        if ($asc >= -20319 && $asc <= -20284)
            return 'A';

        if ($asc >= -20283 && $asc <= -19776)
            return 'B';

        if ($asc >= -19775 && $asc <= -19219)
            return 'C';

        if ($asc >= -19218 && $asc <= -18711)
            return 'D';

        if ($asc >= -18710 && $asc <= -18527)
            return 'E';

        if ($asc >= -18526 && $asc <= -18240)
            return 'F';

        if ($asc >= -18239 && $asc <= -17923)
            return 'G';

        if ($asc >= -17922 && $asc <= -17418)
            return 'H';

        if ($asc >= -17417 && $asc <= -16475)
            return 'J';

        if ($asc >= -16474 && $asc <= -16213)
            return 'K';

        if ($asc >= -16212 && $asc <= -15641)
            return 'L';

        if ($asc >= -15640 && $asc <= -15166)
            return 'M';

        if ($asc >= -15165 && $asc <= -14923)
            return 'N';

        if ($asc >= -14922 && $asc <= -14915)
            return 'O';

        if ($asc >= -14914 && $asc <= -14631)
            return 'P';

        if ($asc >= -14630 && $asc <= -14150)
            return 'Q';

        if ($asc >= -14149 && $asc <= -14091)
            return 'R';

        if ($asc >= -14090 && $asc <= -13319)
            return 'S';

        if ($asc >= -13318 && $asc <= -12839)
            return 'T';

        if ($asc >= -12838 && $asc <= -12557)
            return 'W';

        if ($asc >= -12556 && $asc <= -11848)
            return 'X';

        if ($asc >= -11847 && $asc <= -11056)
            return 'Y';

        if ($asc >= -11055 && $asc <= -10247)
            return 'Z';
        return '#';
    }


    /**
     * @param $i
     * @return mixed
     */
    public static function numToLetter($i)
    {
        $arr = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
        return $arr[$i];
    }

    /**
     * @param $i
     * @return mixed
     */
    public static function letterToNum($i)
    {
        $arr = [
            'A' => 0,
            'B' => 1,
            'C' => 2,
            'D' => 3,
            'E' => 4,
            'F' => 5,
            'G' => 6,
            'H' => 7,
            'I' => 8,
            'J' => 9,
            'K' => 10,
            'L' => 11,
            'M' => 12,
            'N' => 13,
            'O' => 14,
            'P' => 15,
            'Q' => 16,
            'R' => 17,
            'S' => 18,
            'T' => 19,
            'U' => 20,
            'V' => 21,
            'W' => 22,
            'X' => 23,
            'Y' => 24,
            'Z' => 25
            ];
        return $arr[$i];
    }
}
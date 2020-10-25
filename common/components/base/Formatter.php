<?php

namespace common\components\base;

use common\helpers\MoneyHelper;
use \yii\i18n\Formatter as BaseFormatter;

/**
 * Created by PhpStorm.
 * User: hu yang
 * Date: 2018/1/30
 * Time: 上午11:01
 */
class Formatter extends BaseFormatter
{
    public $datetimeFormat = 'php:Y-m-d H:i:s';

    /**
     * @param int $value
     *
     * @return string
     */
    public function asMoney(int $value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }
        return (string)MoneyHelper::f2y($value) . '元';
    }

    /**
     * @param int $value
     *
     * @return string
     */
    public function asArray(array $value): string
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }

}